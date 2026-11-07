<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Config;
use RuntimeException;

final class ImageUpload
{
    private const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Validate and store an uploaded image. Returns the path relative to /public
     * (for example "uploads/donations/9f3c1a.jpg") or null when no file was sent.
     *
     * @param array<string, mixed>|null $file $_FILES entry
     */
    public static function store(?array $file, string $folder = 'donations'): ?string
    {
        if (!is_array($file) || !isset($file['error'])) {
            return null;
        }

        $error = (int) $file['error'];
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('The screenshot could not be uploaded. Please retry.');
        }

        $maxBytes = (int) Config::get('security.upload_max_bytes', 2 * 1024 * 1024);
        if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > $maxBytes) {
            throw new RuntimeException('The screenshot must be smaller than ' . self::humanSize($maxBytes) . '.');
        }

        if (!is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
            throw new RuntimeException('The screenshot upload was not valid.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file((string) $file['tmp_name']);
        $allowed = (array) Config::get('security.upload_mimes', array_keys(self::EXTENSIONS));

        if (!isset(self::EXTENSIONS[$mime]) || !in_array($mime, $allowed, true)) {
            throw new RuntimeException('Only JPG, PNG or WEBP images are allowed.');
        }

        if (@getimagesize((string) $file['tmp_name']) === false) {
            throw new RuntimeException('That file is not a readable image.');
        }

        $folder = trim($folder, '/');
        $directory = ROOT_PATH . '/public/uploads/' . $folder;

        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Upload storage is not writable.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . self::EXTENSIONS[$mime];
        $target = $directory . '/' . $filename;

        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new RuntimeException('The screenshot could not be saved. Please retry.');
        }

        @chmod($target, 0644);

        return 'uploads/' . $folder . '/' . $filename;
    }

    /** Remove a previously stored upload (best effort). */
    public static function delete(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (!str_starts_with($relativePath, 'uploads/') || str_contains($relativePath, '..')) {
            return;
        }

        $file = ROOT_PATH . '/public/' . $relativePath;
        if (is_file($file)) {
            @unlink($file);
        }
    }

    private static function humanSize(int $bytes): string
    {
        return $bytes >= 1048576
            ? round($bytes / 1048576, 1) . ' MB'
            : round($bytes / 1024) . ' KB';
    }
}
