<?php
declare(strict_types=1);

define('DATA_DIR', realpath(__DIR__ . '/../../assets/data'));

function data_file(string $name): string
{
    return DATA_DIR . '/' . $name;
}

function read_json_file(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function write_json_file(string $path, array $data): bool
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    $tmp = $path . '.tmp';
    $fh = fopen($tmp, 'w');
    if (!$fh) {
        return false;
    }
    flock($fh, LOCK_EX);
    fwrite($fh, $json . "\n");
    flock($fh, LOCK_UN);
    fclose($fh);
    return rename($tmp, $path);
}

function read_articles(): array
{
    return read_json_file(data_file('articles.json'));
}

function write_articles(array $data): bool
{
    return write_json_file(data_file('articles.json'), $data);
}

function read_events(): array
{
    return read_json_file(data_file('events.json'));
}

function write_events(array $data): bool
{
    return write_json_file(data_file('events.json'), $data);
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text === '' ? 'item-' . substr(md5((string) microtime(true)), 0, 8) : $text;
}

function unique_slug(array $items, string $baseSlug, ?string $excludeSlug = null): string
{
    $slug = $baseSlug;
    $i = 2;
    while (true) {
        $clash = false;
        foreach ($items as $item) {
            if ($item['slug'] === $slug && $item['slug'] !== $excludeSlug) {
                $clash = true;
                break;
            }
        }
        if (!$clash) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

function find_by_slug(array $items, string $slug): ?array
{
    foreach ($items as $item) {
        if ($item['slug'] === $slug) {
            return $item;
        }
    }
    return null;
}

const MEDIA_IMAGE_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
const MEDIA_VIDEO_EXT = ['mp4', 'webm', 'mov'];
const MEDIA_IMAGE_MAX_BYTES = 8 * 1024 * 1024;
const MEDIA_VIDEO_MAX_BYTES = 60 * 1024 * 1024;

/**
 * Handles an optional uploaded file for $_FILES[$fieldName].
 * Returns ['path' => 'assets/gallery/xxx.jpg'] on success, ['error' => '...'] on failure,
 * or [] if no file was submitted for that field.
 */
function handle_media_upload(string $fieldName, string $slugHint): array
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return [];
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload gagal (kode error: ' . $file['error'] . '). Coba lagi atau pakai file yang lebih kecil.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $isImage = in_array($ext, MEDIA_IMAGE_EXT, true);
    $isVideo = in_array($ext, MEDIA_VIDEO_EXT, true);

    if (!$isImage && !$isVideo) {
        return ['error' => 'Format file "' . $ext . '" tidak didukung. Pakai foto (jpg/png/webp/gif) atau video (mp4/webm/mov).'];
    }

    $maxBytes = $isImage ? MEDIA_IMAGE_MAX_BYTES : MEDIA_VIDEO_MAX_BYTES;
    if ($file['size'] > $maxBytes) {
        $maxMb = (int) ($maxBytes / 1024 / 1024);
        return ['error' => 'Ukuran file terlalu besar (maks ' . $maxMb . 'MB untuk ' . ($isImage ? 'foto' : 'video') . ').'];
    }

    $destDir = $isImage ? 'gallery' : 'video';
    $destAbsDir = dirname(DATA_DIR) . '/' . $destDir;
    if (!is_dir($destAbsDir)) {
        mkdir($destAbsDir, 0755, true);
    }

    $filename = slugify($slugHint) . '-' . substr(md5(uniqid('', true)), 0, 8) . '.' . $ext;
    $destAbsPath = $destAbsDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destAbsPath)) {
        return ['error' => 'Gagal menyimpan file yang di-upload ke server.'];
    }

    return ['path' => 'assets/' . $destDir . '/' . $filename];
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
