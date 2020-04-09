<?php

declare(strict_types=1);

namespace App\Article\Infrastructure\Storage\Mysql;

use App\Article\Domain\Article;
use App\Article\Domain\ArticleCollection;
use App\Article\Domain\Provider\ArticleDataProviderInterface;
use App\Shared\Domain\Criteria;
use App\Shared\Domain\Exception\NotFoundException;
use App\Shared\Infrastructure\Storage\Mysql\AbstractDataProvider;
use App\Shared\Infrastructure\Storage\Mysql\ApplyCriteriaTrait;
use Doctrine\DBAL\Query\QueryBuilder;

final class ArticleDataProvider extends AbstractDataProvider implements ArticleDataProviderInterface
{
    use ApplyCriteriaTrait;

    private array $staticAttributes = [];

    public function getArticleById(int $articleId): ?Article
    {
        $qb = $this->createArticleQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('article.id = :article_id')
            ->setParameter('article_id', $articleId, 'integer')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createArticleFromRow($row);
    }

    public function getArticleBySlug(string $slug): ?Article
    {
        $qb = $this->createArticleQueryBuilder()
            ->setMaxResults(1)
            ->andWhere('article.slug = :slug')
            ->setParameter('slug', $slug, 'string')
        ;

        try {
            $row = $this->fetch($qb);
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->createArticleFromRow($row);
    }

    public function findArticles(?Criteria $criteria = null): ArticleCollection
    {
        $articleCollection = new ArticleCollection();

        $qb = $this->createArticleQueryBuilder();
        $this->applyCriteria($qb, $criteria);

        foreach ($this->fetchAll($qb) as $row) {
            $articleCollection->add($this->createArticleFromRow($row));
        }

        return $articleCollection;
    }

    public function findForSearch(array $keys): ArticleCollection
    {
        $articleCollection = new ArticleCollection();

        $qb = $this->createArticleQueryBuilder();

        foreach ($keys as $id => $key) {
            $qb
                ->andWhere('article.name LIKE :key' . $id . ' OR article.content LIKE :key' . $id)
                ->setParameter('key' . $id, '%' . addcslashes($key, '%_') . '%')
            ;
        }

        $qb->orderBy('article.created_at', 'DESC');

        foreach ($this->fetchAll($qb) as $row) {
            $articleCollection->add($this->createArticleFromRow($row));
        }

        return $articleCollection;
    }

    public function countArticles(): int
    {
        if (array_key_exists('article_count', $this->staticAttributes)) {
            return $this->staticAttributes['article_count'];
        }

        $qb = $this->createQueryBuilder()
            ->select('count(article.id)')
            ->from('articles', 'article')
        ;

        $this->staticAttributes['article_count'] = $this->fetchIntColumn($qb);

        return $this->staticAttributes['article_count'];
    }

    private function createArticleFromRow(array $row): Article
    {
        $row['id'] = (int) $row['id'];
        $row['view'] = (int) $row['view'];
        $row['comment_count'] = (int) $row['comment_count'];
        $row['author'] = !empty($row['author_id']) ? [
            'user_id' => (int) $row['author_id'],
            'first_name' => $row['author_first_name'],
            'last_name' => $row['author_last_name'],
            'avatar' => $row['author_avatar'],
        ] : null;
        unset($row['author_id'], $row['author_first_name'], $row['author_last_name'], $row['author_avatar']);

        return Article::fromArray($row);
    }

    private function createArticleQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select([
                'article.id',
                'article.name',
                'article.content',
                'article.created_at',
                'article.image',
                'article.view',
                'article.slug',
                'article.nb_comments as comment_count',
                'author.id as author_id',
                'author.first_name as author_first_name',
                'author.last_name as author_last_name',
                'author.avatar as author_avatar',
            ])
            ->from('articles', 'article')
            ->leftJoin('article', 'users', 'author', 'author.id = article.author_id')
        ;
    }
}
