<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class AssetHash
{
    public function __construct(
        private string $env
    ) {
    }

    public function get(): string
    {
        $prefix = '?cache=';
        if ('dev' === $this->env) {
            return "{$prefix}develop";
        }

        $filesystem = new Filesystem();
        try {
            $path = '/var/www/public/hash.txt';
            if ($filesystem->exists($path)) {
                $hash = file_get_contents($path);

                return "{$prefix}{$hash}";
            }
        } catch (IOExceptionInterface $exception) {
            //
        }

        return $prefix;
    }
}
