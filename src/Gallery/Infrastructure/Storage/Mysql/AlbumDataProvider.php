<?php

declare(strict_types=1);

namespace App\Gallery\Infrastructure\Storage\Mysql;

use App\Gallery\Domain\Album;
use App\Gallery\Domain\AlbumCollection;
use App\Gallery\Domain\Provider\AlbumDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class AlbumDataProvider extends AbstractDataProvider implements AlbumDataProviderInterface
{
    use ApplyCriteriaTrait;

    public function findAlbumsFromCategoryId(
        ?int $categoryId,
        ?bool $enabled = null,
        ?Criteria $criteria = null
    ): AlbumCollection {
        $albumCollection = new AlbumCollection();

        $qb = $this->createAlbumQueryBuilder();

        if ($categoryId !== null) {
            if ($categoryId === 0) {
                $qb->andWhere('album.category_id IS NULL');
            } else {
                $qb
                    ->andWhere('album.category_id = :category_id')
                    ->setParameter('category_id', $categoryId, 'integer');
            }
        }

        if ($enabled !== null) {
            $qb
                ->andWhere('album.enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0, 'integer')
            ;
        }
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $albumCollection->add($this->createAlbumFromRow($row));
        }

        return $albumCollection;
    }

    public function findForSearch(array $keys): AlbumCollection
    {
        $albumCollection = new AlbumCollection();

        $qb = $this->createAlbumQueryBuilder()
            ->andWhere('album.enabled = :enabled')
            ->setParameter('enabled', 1, 'integer')
            ->orderBy('album.created_at', 'DESC')
        ;

        foreach ($keys as $id => $key) {
            $qb
                ->andWhere('album.name LIKE :key' . $id . ' OR album.relative_path LIKE :key' . $id)
                ->setParameter('key' . $id, '%' . addcslashes($key, '%_') . '%')
            ;
        }

        foreach ($this->fetchAll($qb) as $row) {
            $albumCollection->add($this->createAlbumFromRow($row));
        }

        return $albumCollection;
    }

    public function getAlbumById(int $albumId): ?Album
    {
        $qb = $this->createAlbumQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('album.id = :album_id')
            ->setParameter('album_id', $albumId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createAlbumFromRow($row);
    }

    public function getAlbumBySlug(string $slug): ?Album
    {
        $qb = $this->createAlbumQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('album.slug = :slug')
            ->setParameter('slug', $slug, 'string')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createAlbumFromRow($row);
    }

    public function countAlbums(
        ?int $categoryId = null,
        ?bool $enabled = null
    ): int {
        $qb = $this->createQueryBuilder()
            ->select('count(album.id)')
            ->from('photos_albums', 'album')
        ;

        if ($categoryId !== null) {
            if ($categoryId === 0) {
                $qb->andWhere('album.category_id IS NULL');
            } else {
                $qb
                    ->andWhere('album.category_id = :category_id')
                    ->setParameter('category_id', $categoryId, 'integer');
            }
        }

        if ($enabled !== null) {
            $qb
                ->andWhere('album.enabled = :enabled')
                ->setParameter('enabled', $enabled ? 1 : 0, 'integer')
            ;
        }

        return $this->fetchIntColumn($qb);
    }

    private function createAlbumFromRow(array $row): Album
    {
        $row['id'] = (int) $row['id'];
        $row['enabled'] = (bool) $row['enabled'];
        $row['parent_id'] = (int) $row['category_id'];
        $row['view'] = (int) $row['view'];
        $row['comment_count'] = (int) $row['comment_count'];
        $row['picture_count'] = (int) $row['picture_count'];

        $row['author'] = !empty($row['author_id']) ? [
            'user_id' => (int) $row['author_id'],
            'first_name' => $row['author_first_name'],
            'last_name' => $row['author_last_name'],
            'avatar' => $row['author_avatar'],
        ] : null;
        unset($row['author_id'], $row['author_first_name'], $row['author_last_name'], $row['author_avatar']);

        $row['picture_cover'] = !empty($row['picture_name']) ? [
            'id' => (int) $row['picture_id'],
            'name' => $row['picture_name'],
            'url' => $row['picture_name'],
            'url_min' => null,
            'size' => $row['picture_size'],
        ] : null;
        unset($row['picture_id'], $row['picture_name'], $row['picture_size']);

        return Album::fromArray($row);
    }

    private function createAlbumQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'album.id',
                'album.name',
                'album.category_id',
                'album.enabled',
                'album.slug',
                'album.relative_path',
                'album.view',
                'album.nb_comments as comment_count',
                'album.nb_pictures as picture_count',
                'album.created_at',
                'author.id as author_id',
                'author.first_name as author_first_name',
                'author.last_name as author_last_name',
                'author.avatar as author_avatar',
                'picture.id as picture_id',
                'picture.name as picture_name',
                'picture.size as picture_size',
            ])
            ->from('photos_albums', 'album')
            ->leftJoin('album', 'users', 'author', 'author.id = album.user_id')
            ->leftJoin('album', 'photos_img', 'picture', 'picture.id = album.ref_picture')
        ;
    }
}
