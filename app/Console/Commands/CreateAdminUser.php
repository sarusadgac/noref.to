<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin {--name= : Admin name} {--email= : Admin email}';

    protected $description = 'Create an admin user';

    public function handle(): int
    {
        $name = $this->option('name') ?? text('Name', required: true);
        $email = $this->option('email') ?? text('Email', required: true);
        $password = password('Password', required: true);

        if (User::where('email', $email)->exists()) {
            $this->error("User with email [{$email}] already exists.");

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->role = UserRole::Admin;
        $user->save();
        $user->markEmailAsVerified();

        $this->info("Admin user [{$name}] created successfully.");

        return self::SUCCESS;
    }
}
