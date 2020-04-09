<?php

declare(strict_types=1);

namespace App\Gallery\Infrastructure\Storage\Mysql;

use App\Gallery\Domain\Provider\PictureDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Domain\Image;
use App\Shared\Domain\ImageCollection;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class PictureDataProvider extends AbstractDataProvider implements PictureDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function getPicture(int $pictureId): ?Image
    {
        $qb = $this->createPictureQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('picture.id = :picture_id')
            ->setParameter('picture_id', $pictureId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createImageFromRow($row);
    }

    public function findPicturesByAlbumId(int $albumId, ?Criteria $criteria = null): ImageCollection
    {
        $imageCollection = new ImageCollection();

        $qb = $this->createPictureQueryBuilder()
            ->andWhere('picture.album_id = :album_id')
            ->setParameter('album_id', $albumId, 'integer')
        ;
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $imageCollection->add($this->createImageFromRow($row));
        }

        return $imageCollection;
    }

    public function findOneRandomPicture(int $albumId): ?Image
    {
        $qb = $this->createPictureQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('picture.album_id = :album_id')
            ->setParameter('album_id', $albumId, 'integer')
            ->orderBy('Rand()')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createImageFromRow($row);
    }

    public function countPictures(?int $albumId = null): int
    {
        $qb = $this->createQueryBuilder()
            ->select('count(id)')
            ->from('photos_img', 'picture')
        ;

        if ($albumId !== null) {
            $qb
                ->andWhere('picture.album_id = :album_id')
                ->setParameter('album_id', $albumId, 'integer')
            ;
        }

        return $this->fetchIntColumn($qb);
    }

    private function createImageFromRow(array $row): Image
    {
        $row['id'] = (int) $row['id'];
        $row['url'] = $row['name'];

        return Image::fromArray($row);
    }

    private function createPictureQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'picture.id',
                'picture.name',
                'picture.size',
            ])
            ->from('photos_img', 'picture')
        ;
    }
}
