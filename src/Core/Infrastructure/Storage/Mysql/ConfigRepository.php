<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Storage\Mysql;

use App\Core\Domain\Config;
use App\Core\Domain\ConfigRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;

final class ConfigRepository implements ConfigRepositoryInterface
{
    private Connection $connection;
    private ?Config $configuration;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->configuration = null;
    }

    public function getAll(): array
    {
        if ($this->configuration === null) {
            $this->configuration = $this->fetchConfig();
        }

        return $this->configuration->toArray();
    }

    /**
     * @inheritdoc
     */
    public function get(string $key, $default = null)
    {
        if ($this->configuration === null) {
            $this->configuration = $this->fetchConfig();
        }

        return $this->configuration->get($key, $default);
    }

    /**
     * @inheritdoc
     */
    public function update(string $key, $value): void
    {
        if ($this->configuration === null) {
            $this->configuration = $this->fetchConfig();
        }

        $this->connection->update(
            'configs',
            [$key => $value],
            ['id' => 1]
        );
        $this->configuration->add($key, $value);
    }

    private function fetchConfig(): Config
    {
        $qb = $this->connection->createQueryBuilder()
            ->select([
                'app_name',
                'app_full_name',
                'description',
                'tags',
                'email_address',
                'google_analytics',
                'recaptcha_client',
                'recaptcha_server',
                'facebook_page',
                'linkedin_page',
                'twitter_page',
                'phone_number',
                'postal_address',
                'features',
            ])
            ->from('configs')
            ->setMaxResults(1)
        ;
        /** @var Statement $stm */
        $stm = $qb->execute();

        $configsRow = $stm->fetch();
        $configsRow['features'] = json_decode($configsRow['features'], true);

        return new Config($configsRow);
    }
}
