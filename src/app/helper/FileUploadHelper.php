<?php
declare(strict_types=1);

namespace App\Helpers;

class FileUploadHelper
{
    private const MAX_IMAGE_SIZE = 2097152; // 2 MB
    private const IMAGE_MIME_EXT = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public static function image(string $field, string $subdir): ?string
    {
        if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$field];
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Image upload failed.');
        }
        if ((int) ($file['size'] ?? 0) > self::MAX_IMAGE_SIZE) {
            throw new \RuntimeException('Image must be 2 MB or smaller.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $mime = is_file($tmp) ? (string) mime_content_type($tmp) : '';
        if (!isset(self::IMAGE_MIME_EXT[$mime])) {
            throw new \RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
        }

        $dir = rtrim(UPLOAD_PATH, '/\\') . DIRECTORY_SEPARATOR . trim($subdir, '/\\');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Upload directory is not writable.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::IMAGE_MIME_EXT[$mime];
        $target = $dir . DIRECTORY_SEPARATOR . $name;
        if (!move_uploaded_file($tmp, $target)) {
            throw new \RuntimeException('Could not save uploaded image.');
        }

        return trim($subdir, '/\\') . '/' . $name;
    }

    public static function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $relative = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');
        $file = rtrim(UPLOAD_PATH, '/\\') . DIRECTORY_SEPARATOR . $relative;
        if (is_file($file)) {
            @unlink($file);
        }
    }
}