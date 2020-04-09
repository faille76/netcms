<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\UpdateEnableCategoryCommand;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminEnableCategoryController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private CategoryDataProviderInterface $categoryDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        CategoryDataProviderInterface $categoryDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/gallery/categories/{categoryId}/enable", name="admin_gallery_category_enable", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(Request $request, int $categoryId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $category = $this->categoryDataProvider->getCategoryById($categoryId);
        if ($category === null) {
            throw $this->createNotFoundException();
        }

        $this->commandBus->dispatch(new UpdateEnableCategoryCommand(
            $category->getId(),
            !$category->isEnabled(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_gallery_category_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
