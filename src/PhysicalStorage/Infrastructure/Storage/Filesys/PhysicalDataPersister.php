<?php

declare(strict_types=1);

namespace App\PhysicalStorage\Infrastructure\Storage\Filesys;

use App\PhysicalStorage\Domain\Persister\PhysicalDataPersisterInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\RootViolationException;
use Psr\Log\LoggerInterface;

final class PhysicalDataPersister implements PhysicalDataPersisterInterface
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

    public function add(string $fromFilePath, string $toFilePath): bool
    {
        if ($this->defaultStorage->has($toFilePath)) {
            $this->remove($toFilePath);
        }
        $stream = fopen($fromFilePath, 'r+');
        if ($stream === false) {
            $this->logger->error('Cannot open file "{fromFilePath}".', [
                'fromFilePath' => $fromFilePath,
            ]);

            return false;
        }

        try {
            $res = $this->defaultStorage->writeStream($toFilePath, $stream);
        } catch (FileExistsException $e) {
            $this->logger->error('Cannot write from file "{fromFilePath}" to file "{toFilePath}" because the file already exists.', [
                'fromFilePath' => $fromFilePath,
                'toFilePath' => $toFilePath,
                'e' => $e,
            ]);
            $res = false;
        }
        fclose($stream);

        return $res;
    }

    public function remove(string $filePath): bool
    {
        try {
            $hasBeenRemoved = $this->defaultStorage->delete($filePath);
        } catch (FileNotFoundException $e) {
            $hasBeenRemoved = true;
        }
        if ($hasBeenRemoved === false) {
            $this->logger->error('The file "{filePath}" could not be deleted.', [
                'filePath' => $filePath,
            ]);
        }

        return $hasBeenRemoved;
    }

    public function removeDir(string $dirPath): bool
    {
        try {
            $hasBeenRemoved = $this->defaultStorage->deleteDir($dirPath);
        } catch (RootViolationException $e) {
            $hasBeenRemoved = true;
        }
        if ($hasBeenRemoved === false) {
            $this->logger->error('The directory "{dirPath}" could not be deleted.', [
                'dirPath' => $dirPath,
            ]);
        }

        return $hasBeenRemoved;
    }
}
