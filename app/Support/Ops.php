<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;

/**
 * Post-deploy maintenance for a host without SSH. Called from the token-protected
 * /_ops/deploy endpoint and from Admin → System.
 */
class Ops
{
    public const ACTIONS = [
        'deploy' => ['migrate --force', 'optimize:clear', 'optimize', 'queue:restart'],
        'migrate' => ['migrate --force'],
        'clear-cache' => ['optimize:clear'],
        'cache' => ['optimize'],
        'seed' => ['db:seed --force'], // idempotent: adds missing starter content, never overwrites
    ];

    /** @return array<string, string> command => output */
    public static function run(string $action): array
    {
        $output = [];

        foreach (self::ACTIONS[$action] ?? [] as $command) {
            [$name, $args] = self::parse($command);
            Artisan::call($name, $args);
            $output[$command] = trim(Artisan::output());
        }

        return $output;
    }

    /** @return array{0: string, 1: array<string, mixed>} */
    private static function parse(string $command): array
    {
        $parts = explode(' ', $command);
        $args = [];

        foreach (array_slice($parts, 1) as $flag) {
            $args[$flag] = true;
        }

        return [$parts[0], $args];
    }
}
