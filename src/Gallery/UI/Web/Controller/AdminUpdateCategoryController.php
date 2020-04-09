<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\UpdateCategoryCommand;
use App\Gallery\Application\Query\GetCategoriesTreeQuery;
use App\Gallery\Domain\Provider\CategoryDataProviderInterface;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdateCategoryController extends AbstractController
{
    use UserTrait;
    use QueryHandleTrait;

    private CategoryDataProviderInterface $categoryDataProvider;
    private FeatureFlippingInterface $featureFlipping;
    private MessageBusInterface $commandBus;

    public function __construct(
        CategoryDataProviderInterface $categoryDataProvider,
        FeatureFlippingInterface $featureFlipping,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus
    ) {
        $this->categoryDataProvider = $categoryDataProvider;
        $this->featureFlipping = $featureFlipping;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/admin/gallery/categories/{categoryId}/update", name="admin_gallery_category_update_post", methods={"POST"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function post(Request $request, int $categoryId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $category = $this->categoryDataProvider->getCategoryById($categoryId);
        if ($category === null) {
            throw $this->createNotFoundException();
        }

        $categoryForm = [
            'name' => $request->request->get('name', null),
            'parent_id' => $request->request->getInt('parent_id', 0),
        ];

        $render = [];

        try {
            $this->validate($categoryForm);
            $this->commandBus->dispatch(
                new UpdateCategoryCommand(
                    $category->getId(),
                    $categoryForm['name'],
                    $categoryForm['parent_id'],
                    $this->getUserId(),
                    time()
                )
            );
            $render['succeed'] = 'admin.gallery.category.create.confirm.category_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/gallery_category_create.html.twig', array_merge([
            'name' => $categoryForm['name'],
            'parent_id' => $categoryForm['parent_id'],
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
            'category_id' => $categoryId,
        ], $render));
    }

    /**
     * @Route("/admin/gallery/categories/{categoryId}/update", name="admin_gallery_category_update", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(int $categoryId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $category = $this->categoryDataProvider->getCategoryById($categoryId);
        if ($category === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/gallery_category_create.html.twig', [
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
            'category_id' => $categoryId,
            'name' => $category->getName(),
            'parent_id' => $category->getParentId(),
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $category): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($category['name'])
            ->notEmpty('admin.gallery.category.create.error.name_empty')
        ;
        $lazyAssert->verifyNow();
    }
}
