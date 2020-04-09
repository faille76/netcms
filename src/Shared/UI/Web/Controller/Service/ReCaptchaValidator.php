<?php

declare(strict_types=1);

namespace App\Shared\UI\Web\Controller\Service;

use App\Core\Domain\ConfigRepositoryInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use Symfony\Component\HttpFoundation\Request;

final class ReCaptchaValidator
{
    private FeatureFlippingInterface $featureFlipping;
    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        ConfigRepositoryInterface $configRepository,
        FeatureFlippingInterface $featureFlipping
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->configRepository = $configRepository;
    }

    public function verify(Request $request): bool
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::CAPTCHA)) {
            return true;
        }

        $data = http_build_query(
            [
                'secret' => $this->configRepository->get('recaptcha_server'),
                'response' => $request->request->get('g-recaptcha-response'),
                'remoteip' => $request->server->get('REMOTE_ADDR'),
            ]
        );
        $opts = ['http' =>
            [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data,
            ],
        ];
        $context = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if (!is_string($response)) {
            throw new \RuntimeException('file_get_contents failed.');
        }

        $result = json_decode($response, true);
        if ($result && !$result['success']) {
            return false;
        }

        return true;
    }
}
