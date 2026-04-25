<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3UploadService
{
    /**
     * Upload a file to S3 (or local disk in dev).
     * Returns the storage key (path inside the bucket).
     */
    public function upload(UploadedFile $file, string $folder = 'attachments'): string
    {
        // Generate a unique filename to avoid collisions
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store file — uses FILESYSTEM_DISK from .env (s3 or local)
        $key = Storage::putFileAs($folder, $file, $filename);

        return $key;
    }

    /**
     * Generate a temporary signed URL valid for 30 minutes.
     * On local disk this returns a plain URL instead.
     */
    public function getSignedUrl(string $key): string
    {
        // Local disk doesn't support temporaryUrl — use regular URL
        if (config('filesystems.default') === 'local') {
            return Storage::url($key);
        }

        return Storage::temporaryUrl(
            $key,
            now()->addMinutes(30)
        );
    }

    /**
     * Delete a file from storage.
     */
    public function delete(string $key): void
    {
        Storage::delete($key);
    }
}
