<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\Storage\Mysql;

use App\Page\Domain\Provider\ImagePageDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Image;
use App\Shared\Domain\ImageCollection;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class ImagePageDataProvider extends AbstractDataProvider implements ImagePageDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function findImagesByPageId(int $pageId, ?Criteria $criteria = null): ImageCollection
    {
        $imageCollection = new ImageCollection();

        $qb = $this->createImageQueryBuilder()
            ->andWhere('img.page_id = :page_id')
            ->setParameter('page_id', $pageId, 'integer')
        ;
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $imageCollection->add($this->createImageFromRow($row));
        }

        return $imageCollection;
    }

    public function getImageByIdAndPageId(int $imgId, int $pageId): ?Image
    {
        $qb = $this->createImageQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('img.id = :img_id')
            ->andWhere('img.page_id = :page_id')
            ->setParameter('img_id', $imgId, 'integer')
            ->setParameter('page_id', $pageId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createImageFromRow($row);
    }

    public function getImageById(int $imgId): ?Image
    {
        $qb = $this->createImageQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('img.id = :img_id')
            ->setParameter('img_id', $imgId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createImageFromRow($row);
    }

    private function createImageFromRow(array $row): Image
    {
        $row['id'] = (int) $row['id'];

        return Image::fromArray($row);
    }

    private function createImageQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'img.id',
                'img.name',
                'img.size',
                'img.img as url',
                'img.img_min as url_min',
            ])
            ->from('pages_img', 'img')
        ;
    }
}
