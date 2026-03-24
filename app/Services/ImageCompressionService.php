<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageCompressionService
{
    /**
     * Compress an image stored on a disk in-place.
     *
     * Supported MIME types: image/jpeg, image/png, image/webp, image/gif.
     * PNG compression is lossless (level 6 of 9).
     * JPEG and WebP quality defaults to 75%.
     *
     * @param  string  $storagePath  Relative path on the disk (e.g. "users/photos/abc.jpg")
     * @param  string  $disk         Laravel storage disk name (default: 'public')
     * @param  int     $jpegQuality  JPEG/WebP quality 1-100
     * @return void
     */
    public static function compress(string $storagePath, string $disk = 'public', int $jpegQuality = 75): void
    {
        if (blank($storagePath)) {
            return;
        }

        $absolutePath = Storage::disk($disk)->path($storagePath);

        if (! file_exists($absolutePath)) {
            return;
        }

        $mime = mime_content_type($absolutePath);

        match ($mime) {
            'image/jpeg', 'image/jpg' => self::compressJpeg($absolutePath, $jpegQuality),
            'image/png'               => self::compressPng($absolutePath),
            'image/webp'              => self::compressWebp($absolutePath, $jpegQuality),
            'image/gif'               => self::compressGif($absolutePath),
            default                   => null,
        };
    }

    private static function compressJpeg(string $path, int $quality): void
    {
        $image = @imagecreatefromjpeg($path);
        if ($image === false) {
            return;
        }

        imagejpeg($image, $path, $quality);
        imagedestroy($image);
    }

    private static function compressPng(string $path): void
    {
        $image = @imagecreatefrompng($path);
        if ($image === false) {
            return;
        }

        // Preserve alpha transparency
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // PNG compression level 6 (0=none, 9=max). Level 6 is a good balance.
        imagepng($image, $path, 6);
        imagedestroy($image);
    }

    private static function compressWebp(string $path, int $quality): void
    {
        if (! function_exists('imagecreatefromwebp')) {
            return;
        }

        $image = @imagecreatefromwebp($path);
        if ($image === false) {
            return;
        }

        imagewebp($image, $path, $quality);
        imagedestroy($image);
    }

    private static function compressGif(string $path): void
    {
        $image = @imagecreatefromgif($path);
        if ($image === false) {
            return;
        }

        // GIF has no quality setting; re-saving still strips metadata
        imagegif($image, $path);
        imagedestroy($image);
    }
}
