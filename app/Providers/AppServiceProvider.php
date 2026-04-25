<?php

namespace App\Providers;

use App\Models\Workspace;
use App\Policies\WorkspacePolicy;
use App\Services\S3UploadService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register S3UploadService as a singleton
        $this->app->singleton(S3UploadService::class, fn() => new S3UploadService());
    }

    public function boot(): void
    {
        Gate::policy(Workspace::class, WorkspacePolicy::class);
    }
}
