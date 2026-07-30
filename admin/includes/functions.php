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

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
