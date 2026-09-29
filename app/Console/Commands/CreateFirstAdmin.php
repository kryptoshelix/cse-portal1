<?php

namespace App\Console\Commands;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Secure provisioning of the first (or additional) super administrator.
 * Privileged roles can NEVER be obtained through public registration.
 */
class CreateFirstAdmin extends Command
{
    protected $signature = 'admin:create
                            {--email= : Admin email address}
                            {--name= : Admin display name}';

    protected $description = 'Provision a super administrator account interactively (no credentials are stored in code or seeders)';

    public function handle(): int
    {
        $email = $this->option('email') ?: $this->ask('Admin email address');

        try {
            $this->validate(['email' => $email], [
                'email' => ['required', 'string', 'email', 'max:190', 'unique:users,email'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Invalid email: '.$e->validator->errors()->first('email'));
            return self::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Admin display name', 'Department Administrator');

        if ($existing = User::where('role', UserRole::SuperAdmin->value)->first()) {
            $this->warn("A super administrator already exists ({$existing->email}). Creating an additional one.");
        }

        $password = $this->secret('Admin password (min 10 chars, mixed case, numbers & symbols)');

        try {
            $this->validate(['password' => $password], [
                'password' => ['required', 'string', Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Weak password: '.$e->validator->errors()->first('password'));
            return self::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        // Privileged fields are set exclusively here — never via registration.
        $user->role = UserRole::SuperAdmin;
        $user->status = AccountStatus::Active;
        $user->save();

        app(\App\Services\AuditLogger::class)
            ->log('admin.created', 'Super administrator provisioned via CLI', $user);

        $this->info("Super administrator '{$user->email}' created. Sign in at /login to change anything further through the UI.");
        return self::SUCCESS;
    }
}
