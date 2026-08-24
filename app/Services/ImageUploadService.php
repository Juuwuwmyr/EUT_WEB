<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Store an uploaded image as WebP on the "public" disk, inside the given
     * directory. Any input format supported by GD (JPEG, PNG, GIF, BMP, WebP)
     * is converted, and oversized images are downscaled so we never ship a
     * multi-megabyte photo to a phone.
     *
     * Falls back to storing the original file untouched if GD/WebP support is
     * unavailable or conversion fails for any reason, so an upload never hard
     * fails just because of image optimisation.
     *
     * @return string Relative path within the "public" disk (e.g. "menu-items/abc123.webp")
     */
    public static function storeAsWebp(UploadedFile $file, string $directory, int $maxDimension = 1200, int $quality = 82): string
    {
        try {
            if (!function_exists('imagewebp')) {
                throw new \RuntimeException('GD WebP support is not available on this server.');
            }

            $source = self::createImageResource($file->getRealPath(), $file->getMimeType());
            if (!$source) {
                throw new \RuntimeException('Unsupported or unreadable image file.');
            }

            // Normalise to true-color and preserve transparency (PNG/GIF sources)
            imagepalettetotruecolor($source);
            imagealphablending($source, true);
            imagesavealpha($source, true);

            $width  = imagesx($source);
            $height = imagesy($source);

            if ($width > $maxDimension || $height > $maxDimension) {
                $ratio    = min($maxDimension / $width, $maxDimension / $height);
                $newWidth  = max(1, (int) round($width * $ratio));
                $newHeight = max(1, (int) round($height * $ratio));

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
            }

            $relativePath = trim($directory, '/') . '/' . Str::random(40) . '.webp';
            $fullPath     = Storage::disk('public')->path($relativePath);

            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $ok = imagewebp($source, $fullPath, $quality);
            imagedestroy($source);

            if (!$ok) {
                throw new \RuntimeException('imagewebp() failed to write the converted file.');
            }

            return $relativePath;
        } catch (\Throwable $e) {
            Log::warning('[ImageUploadService] WebP conversion failed, storing original file instead: ' . $e->getMessage());
            return $file->store($directory, 'public');
        }
    }

    /**
     * @return \GdImage|resource|false
     */
    private static function createImageResource(string $path, ?string $mime)
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'               => @imagecreatefrompng($path),
            'image/gif'               => @imagecreatefromgif($path),
            'image/webp'              => @imagecreatefromwebp($path),
            'image/bmp'               => function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : false,
            default                   => false,
        };
    }
}
