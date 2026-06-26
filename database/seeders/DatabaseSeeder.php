<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user for local login.
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        // Create a default workspace and project for the admin user.
        $workspace = Workspace::firstOrCreate(
            ['slug' => 'admin-workspace'],
            [
                'name' => 'Admin Workspace',
                'owner_id' => $admin->id,
            ]
        );

        if (! $workspace->members()->where('user_id', $admin->id)->exists()) {
            $workspace->members()->attach($admin->id, ['role' => 'admin']);
        }

        Project::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'Default Project'],
            [
                'created_by' => $admin->id,
                'description' => 'Default project for the admin workspace.',
                'status' => 'active',
            ]
        );
    }
}
