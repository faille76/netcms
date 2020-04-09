<?php

declare(strict_types=1);

namespace App\Partner\Infrastructure\Storage\Mysql;

use App\Partner\Domain\Persister\PartnerDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class PartnerDataPersister extends AbstractDataPersister implements PartnerDataPersisterInterface
{
    public function createPartner(string $name, string $url, string $image, bool $enabled): int
    {
        $this->insert('partners', [
            'name' => $name,
            'url' => $url,
            'image' => $image,
            'enabled' => $enabled ? 1 : 0,
        ]);

        return $this->getLastInsertId();
    }

    public function deletePartner(int $partnerId): void
    {
        $this->delete('partners', [
            'id' => $partnerId,
        ]);
    }

    public function updatePartner(int $partnerId, string $name, string $url, string $image): void
    {
        $this->update('partners', [
            'name' => $name,
            'url' => $url,
            'image' => $image,
        ], [
            'id' => $partnerId,
        ]);
    }

    public function updatePartnerEnabled(int $partnerId, bool $enabled): void
    {
        $this->update('partners', [
            'enabled' => $enabled ? 1 : 0,
        ], [
            'id' => $partnerId,
        ]);
    }
}
