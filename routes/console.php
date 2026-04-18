<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('import:universaltea {--dry-run}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $this->components->info('Starting Universal Tea import pipeline scaffold...');
    $this->components->twoColumnDetail('Mode', $dryRun ? 'Dry run' : 'Execution');
    $this->components->twoColumnDetail('Step 1', 'Export PostgreSQL data from legacy Supabase source');
    $this->components->twoColumnDetail('Step 2', 'Map auth.users + profiles into Laravel users');
    $this->components->twoColumnDetail('Step 3', 'Map legacy roles into spatie/laravel-permission tables');
    $this->components->twoColumnDetail('Step 4', 'Import catalog, orders, visit sessions, chat sessions, chat messages');
    $this->components->twoColumnDetail('Step 5', 'Mirror storage assets into public disk paths');
    $this->components->twoColumnDetail('Step 6', 'Rewrite external URLs into local filesystem paths');
    $this->newLine();
    $this->warn('Import execution logic is still a scaffold. Connect the legacy export source and mapping services next.');
})->purpose('Run the Universal Tea migration pipeline scaffold');

Artisan::command('universaltea:bootstrap-admin {email=admin@universaltea.test} {password=password}', function (string $email, string $password) {
    $role = Role::firstOrCreate(['name' => config('universaltea.roles.admin')]);

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'full_name' => 'Universal Tea Admin',
            'password' => Hash::make($password),
            'is_active' => true,
        ],
    );

    $user->assignRole($role);

    $this->components->info('Admin account is ready.');
    $this->components->twoColumnDetail('Email', $email);
})->purpose('Bootstrap an admin account for the Universal Tea clone');
