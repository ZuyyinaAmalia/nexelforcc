<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get public URL for file stored in Supabase
     * Returns placeholder image if file doesn't exist
     */
    public static function getPublicUrl(?string $path): ?string
    {
        if (!$path) {
            return self::getPlaceholderImage();
        }

        // If already a full URL, return placeholder for localhost (file not in cloud)
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            if (str_contains($path, 'localhost') || str_contains($path, '127.0.0.1')) {
                return self::getPlaceholderImage();
            }
            // For Supabase URLs - all old URLs return placeholder
            // Only NEW uploads after this fix (timestamp > 1764845700 = Dec 4, 2025 after fix) will work
            if (str_contains($path, 'supabase.co')) {
                // Check if it's a new upload after fix deployment
                preg_match('/\/(\d+)_/', $path, $matches);
                if ($matches && isset($matches[1])) {
                    $timestamp = (int)$matches[1];
                    if ($timestamp > 1764845700) {
                        // New file after fix, return URL as is
                        return $path;
                    }
                }
                // Old file (uploaded before fix) or no timestamp found, return placeholder
                return self::getPlaceholderImage();
            }
            // Other external URLs
            return $path;
        }

        // Check if using Supabase disk
        if (config('filesystems.default') === 'supabase') {
            // Build Supabase public URL manually
            $projectId = config('filesystems.disks.supabase.project_id') ?? env('SUPABASE_PROJECT_ID');
            $bucket = config('filesystems.disks.supabase.bucket') ?? env('SUPABASE_BUCKET');
            
            // Remove leading slash if exists
            $path = ltrim($path, '/');
            
            return "https://{$projectId}.supabase.co/storage/v1/object/public/{$bucket}/{$path}";
        }

        // Fallback to local storage URL  
        return asset('storage/' . $path);
    }

    /**
     * Get placeholder image URL
     * Returns a generic product placeholder from Unsplash
     */
    private static function getPlaceholderImage(): string
    {
        // Simple, consistent placeholder for products without images
        return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop&q=80';
    }

    /**
     * Upload file to Supabase Storage
     */
    public static function uploadFile($file, string $directory): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;
        
        Storage::disk('supabase')->put($path, file_get_contents($file), 'public');
        
        return $path;
    }

    /**
     * Delete file from Supabase Storage
     */
    public static function deleteFile(?string $path): bool
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (Storage::disk('supabase')->exists($path)) {
            return Storage::disk('supabase')->delete($path);
        }

        return false;
    }
}
