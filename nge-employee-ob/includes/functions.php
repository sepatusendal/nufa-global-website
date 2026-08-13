<?php
declare(strict_types=1);

define('DATA_DIR', realpath(__DIR__ . '/../../assets/data'));
define('MEMO_DIR', dirname(DATA_DIR, 2) . '/memo-storage');

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

function read_employees(): array
{
    return read_json_file(data_file('employees.json'));
}

function read_memos(): array
{
    return read_json_file(data_file('memos.json'));
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
