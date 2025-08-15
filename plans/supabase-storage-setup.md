# Supabase Storage Integration Plan

## Step 1: Install Required Packages

```bash
composer require league/flysystem-aws-s3-v3
composer require aws/aws-sdk-php #do not install this if you run the first command
```

## Step 2: Update Environment Variables

Add to your `.env`:
```env
# Supabase Storage Configuration
SUPABASE_URL=https://your-project-ref.supabase.co
SUPABASE_KEY=your-anon-key
SUPABASE_SERVICE_KEY=your-service-role-key
SUPABASE_STORAGE_BUCKET=exam-papers
```

## Step 3: Configure Filesystem (Updated)

Update your `config/filesystems.php`:

```php
'disks' => [
    // Keep your existing disks...

    'supabase' => [
        'driver' => 's3',
        'key' => env('SUPABASE_SERVICE_KEY'),
        'secret' => '', // Not used by Supabase
        'region' => 'us-east-1', // Supabase uses this format
        'bucket' => env('SUPABASE_STORAGE_BUCKET', 'exam-papers'),
        'url' => env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_STORAGE_BUCKET'),
        'endpoint' => env('SUPABASE_URL') . '/storage/v1/s3',
        'use_path_style_endpoint' => true,
        'throw' => false,
    ],
],
```

## Step 4: Create Supabase Storage Bucket

In Supabase Dashboard:
1. Go to Storage
2. Create bucket: `exam-papers`
3. Set as **Private** (we'll handle access through signed URLs)
4. Enable RLS (Row Level Security)

## Step 5: Update Your Models

Update your `Paper` model to use Supabase storage:

```php
// In your Paper model
class Paper extends Model
{
    // Add this method to get file URL
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        // For private papers, generate signed URL
        if (!$this->is_public) {
            return $this->generateSignedUrl();
        }

        // For public papers, you can still use signed URLs for consistency
        // or return public URL directly
        return $this->generateSignedUrl();
    }

    private function generateSignedUrl($expiresIn = 3600) // 1 hour
    {
        $supabase = new \Supabase\CreateClient(
            config('app.supabase_url'),
            config('app.supabase_service_key')
        );

        return $supabase->storage
            ->from(config('filesystems.disks.supabase.bucket'))
            ->createSignedUrl($this->file_path, $expiresIn);
    }
}
```

## Step 6: Create Supabase Service

Create `app/Services/SupabaseStorageService.php`:

```php
<?php

namespace App\Services;

use Supabase\CreateClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    private $supabase;
    private $bucket;

    public function __construct()
    {
        $this->supabase = new CreateClient(
            config('app.supabase_url'),
            config('app.supabase_service_key')
        );

        $this->bucket = config('filesystems.disks.supabase.bucket');
    }

    public function uploadFile(UploadedFile $file, $directory = 'papers')
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        $response = $this->supabase->storage
            ->from($this->bucket)
            ->upload($path, file_get_contents($file->getRealPath()), [
                'contentType' => $file->getMimeType(),
                'cacheControl' => '3600'
            ]);

        if ($response['error']) {
            throw new \Exception('Failed to upload file: ' . $response['error']['message']);
        }

        return $path;
    }

    public function deleteFile($path)
    {
        return $this->supabase->storage
            ->from($this->bucket)
            ->remove([$path]);
    }

    public function generateSignedUrl($path, $expiresIn = 3600)
    {
        return $this->supabase->storage
            ->from($this->bucket)
            ->createSignedUrl($path, $expiresIn);
    }

    public function getPublicUrl($path)
    {
        return $this->supabase->storage
            ->from($this->bucket)
            ->getPublicUrl($path);
    }
}
```

## Step 7: Update Your Livewire Components

Example upload component update:

```php
// In your PaperUploader component
use App\Services\SupabaseStorageService;

class PaperUploader extends Component
{
    protected $supabaseStorage;

    public function boot()
    {
        $this->supabaseStorage = app(SupabaseStorageService::class);
    }

    public function save()
    {
        $this->validate();

        try {
            // Upload to Supabase
            $filePath = $this->supabaseStorage->uploadFile($this->file);

            // Create paper record
            Paper::create([
                'title' => $this->title,
                'file_path' => $filePath,
                'file_size_bytes' => $this->file->getSize(),
                // ... other fields
            ]);

            session()->flash('message', 'Paper uploaded successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Upload failed: ' . $e->getMessage());
        }
    }
}
```

## Benefits of This Approach

1. **Seamless Authentication**: All file access still goes through your Laravel app
2. **Flexible Privacy**: Easy to toggle between public/private per paper
3. **Performance**: Direct file serving from Supabase CDN
4. **Security**: Signed URLs expire, preventing unauthorized sharing
5. **Scalability**: Supabase handles file storage scaling
6. **Cost**: Only pay for what you use

## Migration Strategy

Since you have no existing files:
1. Set up Supabase storage configuration
2. Update your upload components
3. Test with a few sample uploads
4. Deploy to Render with new configuration

## Configuration Summary

Your updated `.env` will need:
```env
FILESYSTEM_DISK=supabase
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_KEY=your-anon-key
SUPABASE_SERVICE_KEY=your-service-role-key
SUPABASE_STORAGE_BUCKET=exam-papers
```
