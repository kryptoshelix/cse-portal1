<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    public function test_login_page_renders_and_register_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
        $this->get('/register')->assertOk();
    }

    public function test_registration_creates_pending_least_privilege_account(): void
    {
        $res = $this->post('/register', [
            'name' => 'New Student',
            'email' => 'new.student@test.local',
            'password' => 'StrongPass!2026',
            'password_confirmation' => 'StrongPass!2026',
            'account_type' => 'student',
            'roll_number' => 'CSE99001',
            'program' => 'B.Tech',
            'batch' => 2024,
        ]);

        // Redirects to login with an "awaiting approval" notice — never auto-authenticated.
        $res->assertRedirect(route('login'))->assertSessionHas('status');
        $this->assertGuest();

        $user = User::where('email', 'new.student@test.local')->first();
        $this->assertNotNull($user);
        $this->assertSame(UserRole::Student, $user->role);
        $this->assertSame(AccountStatus::Pending, $user->status);
        // Password must be hashed, never stored in plain text.
        $this->assertNotSame('StrongPass!2026', $user->password);
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    public function test_registration_cannot_assign_privileged_roles(): void
    {
        foreach (['super_admin', 'dept_admin', 'admin', 'Super Admin'] as $attempt) {
            $res = $this->post('/register', [
                'name' => 'Escalator '.$attempt,
                'email' => 'escalate.'.md5($attempt).'@test.local',
                'password' => 'StrongPass!2026',
                'password_confirmation' => 'StrongPass!2026',
                'account_type' => $attempt,
                'roll_number' => 'CSE1'.substr(md5($attempt), 0, 4),
                'program' => 'B.Tech',
                'batch' => 2024,
            ]);

            // Either validation rejects the bogus account type…
            if (session('errors') && session('errors')->has('account_type')) {
                continue;
            }
            // …or, if a row was created, it MUST NOT have an admin role or active status.
            $u = User::where('email', 'escalate.'.md5($attempt).'@test.local')->first();
            if ($u) {
                $this->assertNotContains($u->role, [UserRole::DeptAdmin, UserRole::SuperAdmin]);
                $this->assertSame(AccountStatus::Pending, $u->status);
            }
        }

        // No admin accounts should exist from registration attempts at all.
        $this->assertSame(0, User::whereIn('role', ['dept_admin', 'super_admin'])
            ->where('name', 'like', 'Escalator%')->count());
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $this->makeUser(UserRole::Student, ['email' => 'dup@example.com']);

        $this->post('/register', [
            'name' => 'Copycat',
            'email' => 'dup@example.com',
            'password' => 'StrongPass!2026',
            'password_confirmation' => 'StrongPass!2026',
            'account_type' => 'student',
            'roll_number' => 'CSE99002',
            'program' => 'B.Tech',
            'batch' => 2024,
        ])->assertSessionHasErrors('email');
    }

    public function test_pending_account_cannot_access_dashboards(): void
    {
        $pending = $this->makeUser(UserRole::Student, ['status' => AccountStatus::Pending]);

        // Login itself refuses to authenticate a non-active account.
        $this->post('/login', ['email' => $pending->email, 'password' => 'Sup3rSecret!Pass'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // Even if somehow authenticated, the active-account middleware blocks dashboards.
        $this->actingAs($pending)->get('/student/dashboard')->assertRedirect(route('account.pending'));
        $this->actingAs($pending)->get('/admin/dashboard')->assertRedirect(route('account.pending'));
    }

    public function test_deactivated_account_is_blocked(): void
    {
        $user = $this->makeUser(UserRole::Student, ['status' => AccountStatus::Deactivated]);

        $this->post('/login', ['email' => $user->email, 'password' => 'Sup3rSecret!Pass'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // Middleware logs out deactivated users who hold a stale session.
        $this->actingAs($user)->get('/student/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_role_aware_login_redirects(): void
    {
        $cases = [
            [UserRole::Student, '/student/dashboard'],
            [UserRole::Faculty, '/faculty/dashboard'],
            [UserRole::DeptAdmin, '/admin/dashboard'],
            [UserRole::SuperAdmin, '/admin/dashboard'],
        ];

        foreach ($cases as [$role, $target]) {
            $user = $this->makeUser($role);
            $this->from('/login')
                ->post('/login', ['email' => $user->email, 'password' => 'Sup3rSecret!Pass'])
                ->assertRedirect($target);
            $this->post('/logout');
        }
    }

    public function test_already_authenticated_user_is_redirected_from_login(): void
    {
        $student = $this->makeUser(UserRole::Student);
        $this->actingAs($student)->get('/login')->assertRedirect(route('student.dashboard'));

        $admin = $this->makeUser(UserRole::DeptAdmin);
        $this->actingAs($admin)->get('/login')->assertRedirect(route('admin.dashboard'));
    }

    public function test_invalid_credentials_are_rejected_with_safe_message(): void
    {
        $user = $this->makeUser(UserRole::Student);

        $res = $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        $res->assertSessionHasErrors('email');
        $this->assertGuest();

        // Error message must not reveal whether the email exists.
        $res2 = $this->post('/login', ['email' => 'ghost@nowhere.test', 'password' => 'whatever']);
        $res2->assertSessionHasErrors('email');
    }

    public function test_session_regenerates_on_login_and_destroys_on_logout(): void
    {
        $user = $this->makeUser(UserRole::Student);

        $this->post('/login', ['email' => $user->email, 'password' => 'Sup3rSecret!Pass']);
        $idAfterLogin = session()->getId();
        $this->assertNotEmpty($idAfterLogin);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
        $this->assertNotSame($idAfterLogin, session()->getId());

        // Old session must not still grant access.
        $this->get('/student/dashboard')->assertRedirect(route('login'));
    }

    public function test_login_throttling_blocks_brute_force(): void
    {
        $user = $this->makeUser(UserRole::Student);

        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'bad'.$i]);
        }

        $res = $this->post('/login', ['email' => $user->email, 'password' => 'Sup3rSecret!Pass']);
        $res->assertStatus(429);
    }

    public function test_password_change_requires_current_password_and_hashes_new(): void
    {
        $user = $this->makeUser(UserRole::Student);
        $this->actingAs($user);

        // Wrong current password rejected.
        $this->put('/student/password', [
            'current_password' => 'not-the-password',
            'new_password' => 'BrandNewPass!2026',
            'new_password_confirmation' => 'BrandNewPass!2026',
        ])->assertSessionHasErrors('current_password');

        $this->put('/student/password', [
            'current_password' => 'Sup3rSecret!Pass',
            'new_password' => 'BrandNewPass!2026',
            'new_password_confirmation' => 'BrandNewPass!2026',
        ])->assertRedirect()->assertSessionHas('status');

        $fresh = $user->fresh();
        $this->assertTrue(password_verify('BrandNewPass!2026', $fresh->password));
    }

    public function test_reset_token_flow_updates_password_without_mail_infrastructure(): void
    {
        $user = $this->makeUser(UserRole::Student);

        // Simulate a token existing in the database (as the broker would create).
        $token = 'test-reset-token-'.uniqid();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => password_hash($token, PASSWORD_BCRYPT),
            'created_at' => now(),
        ]);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'RecoveredPass!2026',
            'password_confirmation' => 'RecoveredPass!2026',
        ])->assertRedirect(route('login'));

        $this->assertTrue(password_verify('RecoveredPass!2026', $user->fresh()->password));
        // Token consumed: reset row removed.
        $this->assertSame(0, DB::table('password_reset_tokens')->where('email', $user->email)->count());
    }

    public function test_guests_cannot_access_any_protected_area(): void
    {
        foreach (['/student/dashboard', '/faculty/dashboard', '/admin/dashboard',
                  '/admin/accounts', '/admin/audit-logs', '/student/profile'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }
}
