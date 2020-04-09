<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\Storage\Mysql;

use App\Page\Domain\Persister\ImagePageDataPersisterInterface;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataPersister;

final class ImagePageDataPersister extends AbstractDataPersister implements ImagePageDataPersisterInterface
{
    public function deleteByPageId(int $pageId): void
    {
        $this->delete('pages_img', [
            'page_id' => $pageId,
        ]);
    }

    public function deleteById(int $id): void
    {
        $this->delete('pages_img', [
            'id' => $id,
        ]);
    }

    public function createImgPage(int $pageId, string $name, string $image, string $size): int
    {
        $this->insert('pages_img', [
            'page_id' => $pageId,
            'name' => $name,
            'img' => $image,
            'size' => $size,
        ]);

        return $this->getLastInsertId();
    }

    public function updateImgPage(int $id, string $name): void
    {
        $this->update('pages_img', [
            'name' => $name,
        ], [
            'id' => $id,
        ]);
    }
}
