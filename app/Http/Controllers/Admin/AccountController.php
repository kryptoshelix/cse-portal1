<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Account approval, status management and (super-admin only) role management.
 */
class AccountController extends Controller
{
    public function __construct(private AuditLogger $audit)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $q = User::with(['student', 'facultyProfile'])
            ->when(trim((string) $request->query('q')), function ($qq) use ($request) {
                $like = '%'.addcslashes(trim((string) $request->query('q')), '%_').'%';
                $qq->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->when($request->query('role') && in_array($request->query('role'), UserRole::values(), true),
                fn ($qq) => $qq->where('role', $request->query('role')))
            ->when($request->query('status') && in_array($request->query('status'), AccountStatus::values(), true),
                fn ($qq) => $qq->where('status', $request->query('status')));

        return view('admin.accounts.index', [
            'items' => $q->latest()->paginate(15)->withQueryString(),
            'roles' => UserRole::cases(),
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function show(User $user)
    {
        $this->authorize('viewAny', User::class);

        return view('admin.accounts.show', ['user' => $user->load(['student', 'facultyProfile'])]);
    }

    public function setStatus(Request $request, User $user)
    {
        $this->authorize('approve', User::class);

        $data = $request->validate([
            'status' => ['required', Rule::in([AccountStatus::Active->value, AccountStatus::Rejected->value, AccountStatus::Deactivated->value])],
        ]);

        $new = AccountStatus::from($data['status']);

        if (! auth()->user()->can('deactivate', $user) && $new !== AccountStatus::Active && $new !== AccountStatus::Rejected) {
            abort(403);
        }

        // Dept admins may activate pending accounts but never reactivate super admins they cannot manage.
        if ($new === AccountStatus::Deactivated || ($user->status === AccountStatus::Deactivated && $new === AccountStatus::Active)) {
            $this->authorize('deactivate', $user);
        }

        $old = $user->status->value;
        $user->forceFill(['status' => $new])->save();

        $this->audit->log('account.status_changed', "Account #{$user->id} status: {$old} -> {$new->value}", $user,
            ['before' => $old, 'after' => $new->value]);

        return back()->with('success', "Account updated to “{$new->value}”.");
    }

    public function setRole(Request $request, User $user)
    {
        // Super admin only — enforced by policy AND route middleware.
        $this->authorize('manageRoles', User::class);

        $data = $request->validate([
            'role' => ['required', Rule::in(UserRole::values())],
        ]);

        $new = UserRole::from($data['role']);
        $old = $user->role->value;

        // Safeguard: never demote the last active super administrator.
        if ($old === UserRole::SuperAdmin->value && $new !== UserRole::SuperAdmin) {
            $remaining = User::where('role', UserRole::SuperAdmin->value)
                ->where('status', AccountStatus::Active->value)
                ->where('id', '!=', $user->id)->count();
            if ($remaining === 0) {
                return back()->withErrors(['role' => 'Cannot demote the last remaining active super administrator.']);
            }
        }

        $user->forceFill(['role' => $new])->save();

        $this->audit->log('account.role_changed', "Account #{$user->id} role: {$old} -> {$new->value}", $user,
            ['before' => $old, 'after' => $new->value]);

        return back()->with('success', 'Role updated.');
    }

    public function setReviewFlag(Request $request, User $user)
    {
        $this->authorize('manageRoles', User::class);

        $flag = $request->boolean('can_review_achievements');
        $user->forceFill(['can_review_achievements' => $flag])->save();

        $this->audit->log('account.review_permission_changed',
            "Account #{$user->id} review permission set to ".($flag ? 'granted' : 'revoked'), $user);

        return back()->with('success', 'Review permission updated.');
    }

    public function auditLogs(Request $request)
    {
        $q = AuditLog::with('actor')->latest();
        if ($search = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('action', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%'));
        }

        return view('admin.accounts.audit', ['items' => $q->paginate(30)->withQueryString()]);
    }
}
