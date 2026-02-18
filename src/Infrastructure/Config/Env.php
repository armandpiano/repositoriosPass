<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

class Env
{
    /** @var array<string, string> */
    private array $vars = [];

    public function __construct(string $envFile)
    {
        if (is_file($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '' || strpos($trimmed, '#') === 0) {
                        continue;
                    }

                    $parts = explode('=', $trimmed, 2);
                    if (count($parts) === 2) {
                        $this->vars[trim($parts[0])] = trim($parts[1]);
                    }
                }
            }
        }
    }

    public function get(string $key, string $default = ''): string
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        $value = getenv($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return $default;
    }

    public function getInt(string $key, int $default): int
    {
        $value = $this->get($key, (string) $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    public function getBool(string $key, bool $default): bool
    {
        $value = strtolower($this->get($key, $default ? 'true' : 'false'));

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }
}
