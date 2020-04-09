<?php

declare(strict_types=1);

namespace App\PhysicalStorage\Infrastructure\Storage\Filesys;

use App\PhysicalStorage\Domain\Provider\PhysicalDataProviderInterface;
use App\Shared\Domain\Exception\NotFoundException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;

final class PhysicalDataProvider implements PhysicalDataProviderInterface
{
    private FilesystemInterface $defaultStorage;
    private LoggerInterface $logger;

    public function __construct(
        FilesystemInterface $defaultStorage,
        LoggerInterface $logger
    ) {
        $this->defaultStorage = $defaultStorage;
        $this->logger = $logger;
    }

    public function get(string $filePath): string
    {
        try {
            $stream = $this->defaultStorage->readStream($filePath);
        } catch (FileNotFoundException $e) {
            throw new NotFoundException('File "' . $filePath . '" does not exists.');
        }
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('File not found.');
        }

        $tmpFilePath = tempnam(
            sys_get_temp_dir(),
            ''
        );
        if (!is_string($tmpFilePath)) {
            throw new \RuntimeException('Cannot create temporary file.');
        }

        $tmpStream = fopen($tmpFilePath, 'w');
        if ($tmpStream === false) {
            throw new \RuntimeException('Cannot open file ' . $tmpFilePath);
        }
        stream_copy_to_stream($stream, $tmpStream);
        fclose($tmpStream);
        fclose($stream);

        return $tmpFilePath;
    }

    public function has(string $filePath): bool
    {
        return $this->defaultStorage->has($filePath);
    }
}
