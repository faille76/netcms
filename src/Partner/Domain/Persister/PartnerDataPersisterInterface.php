<?php

declare(strict_types=1);

namespace App\Partner\Domain\Persister;

interface PartnerDataPersisterInterface
{
    public function createPartner(string $name, string $url, string $image, bool $enabled): int;

    public function deletePartner(int $partnerId): void;

    public function updatePartner(int $partnerId, string $name, string $url, string $image): void;

    public function updatePartnerEnabled(int $partnerId, bool $enabled): void;
}
