<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Crop image to square, resize, and save as WebP.
     * Returns the relative path inside 'public' disk.
     */
    public static function resizeAndCropToSquare(UploadedFile $file, string $directory, int $size = 150): ?string
    {
        $mime = $file->getMimeType();
        $source = null;

        // Load image based on mime type
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $source = @imagecreatefromjpeg($file->getRealPath());
                break;
            case 'image/png':
                $source = @imagecreatefrompng($file->getRealPath());
                break;
            case 'image/webp':
                $source = @imagecreatefromwebp($file->getRealPath());
                break;
            case 'image/gif':
                $source = @imagecreatefromgif($file->getRealPath());
                break;
            default:
                return null;
        }

        if (!$source) {
            return null;
        }

        // Get original dimensions
        $width = imagesx($source);
        $height = imagesy($source);

        // Calculate square crop coordinates
        if ($width > $height) {
            $cropSize = $height;
            $x = (int) (($width - $height) / 2);
            $y = 0;
        } else {
            $cropSize = $width;
            $x = 0;
            $y = (int) (($height - $width) / 2);
        }

        // Create square destination image
        $target = imagecreatetruecolor($size, $size);

        // Handle transparency for PNG/WebP
        imagealphablending($target, false);
        imagesavealpha($target, true);

        // Crop and resize
        imagecopyresampled(
            $target,
            $source,
            0, 0,
            $x, $y,
            $size, $size,
            $cropSize, $cropSize
        );

        // Create directory if not exists
        Storage::disk('public')->makeDirectory($directory);

        // Generate filename
        $filename = uniqid('img_', true) . '.webp';
        $fullPath = Storage::disk('public')->path($directory . '/' . $filename);

        // Save as WebP
        imagewebp($target, $fullPath, 90);

        // Free memory
        imagedestroy($source);
        imagedestroy($target);

        return $directory . '/' . $filename;
    }
}
