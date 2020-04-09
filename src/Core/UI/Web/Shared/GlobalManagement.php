<?php

declare(strict_types=1);

namespace App\Core\UI\Web\Shared;

use App\Core\Domain\ConfigRepositoryInterface;
use App\Core\Domain\FeatureFlippingInterface;
use App\Page\Application\Query\GetNavPagesTreeQuery;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class GlobalManagement
{
    use QueryHandleTrait;

    private ConfigRepositoryInterface $configRepository;
    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        ConfigRepositoryInterface $configRepository,
        MessageBusInterface $queryBus
    ) {
        $this->configRepository = $configRepository;
        $this->featureFlipping = $featureFlipping;
        $this->queryBus = $queryBus;
    }

    public function getConfig(): array
    {
        return [
            'APP_NAME' => $this->configRepository->get('app_name'),
            'APP_NAME_FULL' => $this->configRepository->get('app_full_name'),
            'APP_DESCRIPTION' => $this->configRepository->get('description'),
            'APP_TAGS' => $this->configRepository->get('tags'),
            'URL_HOMEPAGE' => $_ENV['APP_URL'],
            'CHARSET' => $_ENV['CHARSET'] ?? 'UTF-8',
            'APP_AUTHOR' => $_ENV['APP_AUTHOR'],
            'APP_AUTHOR_EMAIL' => $_ENV['APP_AUTHOR_EMAIL'],
            'APP_VERSION' => $_ENV['APP_VERSION'],
            'ANALYTICS_TAG' => $this->configRepository->get('google_analytics'),
            'CAPTCHA_CLIENT' => $this->configRepository->get('recaptcha_client'),
            'FACEBOOK_PAGE_URL' => $this->configRepository->get('facebook_page'),
            'TWITTER_PAGE_URL' => $this->configRepository->get('twitter_page'),
            'LINKEDIN_PAGE_URL' => $this->configRepository->get('linkedin_page'),
            'PHONE_NUMBER' => $this->configRepository->get('phone_number'),
            'POSTAL_ADDRESS' => $this->configRepository->get('postal_address'),
            'EMAIL_ADDRESS' => $this->configRepository->get('email_address'),
        ];
    }

    public function getNavBar(string $lang): array
    {
        return $this->handleQuery(new GetNavPagesTreeQuery($lang));
    }

    public function getFeature(): array
    {
        return $this->featureFlipping->getModules();
    }
}
