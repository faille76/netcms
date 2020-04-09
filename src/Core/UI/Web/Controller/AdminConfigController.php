<?php

declare(strict_types=1);

namespace App\Core\UI\Web\Controller;

use App\Core\Domain\ConfigRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminConfigController extends AbstractController
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        ConfigRepositoryInterface $configRepository
    ) {
        $this->configRepository = $configRepository;
    }

    /**
     * @Route("/admin/config/feature/{featureName}", name="admin_config_feature_enable", methods={"GET"})
     * @IsGranted("ROLE_CONFIGURATION")
     */
    public function enable(Request $request, string $featureName): Response
    {
        $parameters = $this->configRepository->getAll();
        if (isset($parameters['features'][$featureName])) {
            $parameters['features'][$featureName] = !$parameters['features'][$featureName];
        }

        $this->configRepository->update('features', json_encode($parameters['features']));

        return $this->redirectToRoute('admin_config.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }

    /**
     * @Route("/admin/config", name="admin_config_post", methods={"POST"})
     * @IsGranted("ROLE_CONFIGURATION")
     */
    public function post(Request $request): Response
    {
        $parameters = $this->configRepository->getAll();
        unset($parameters['features']);
        foreach ($parameters as $key => $value) {
            $input = $request->request->get($key, null);
            if ($input !== $value) {
                $this->configRepository->update($key, $input);
            }
        }

        return $this->redirectToRoute('admin_config.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }

    /**
     * @Route("/admin/config", name="admin_config", methods={"GET"})
     * @IsGranted("ROLE_CONFIGURATION")
     */
    public function __invoke(): Response
    {
        $parameters = $this->configRepository->getAll();
        unset($parameters['features']);

        return $this->render('admin/config.html.twig', [
            'parameters' => $parameters,
        ]);
    }
}
