<?php

declare(strict_types=1);

namespace App\User\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\User\Domain\Provider\UserDataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private UserDataProviderInterface $userDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        UserDataProviderInterface $userDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->userDataProvider = $userDataProvider;
    }

    /**
     * @Route("/profile/{userId}", name="user_profile_information", methods={"GET"})
     */
    public function __invoke(int $userId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PROFILE_DETAILS)) {
            throw $this->createNotFoundException();
        }

        $user = $this->userDataProvider->getUserById($userId);
        if ($user === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('profile/details.html.twig', [
            'user' => $user,
        ]);
    }
}
