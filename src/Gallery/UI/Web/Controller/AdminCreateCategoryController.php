<?php

declare(strict_types=1);

namespace App\Gallery\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Gallery\Application\Command\CreateCategoryCommand;
use App\Gallery\Application\Query\GetCategoriesTreeQuery;
use App\Gallery\Domain\Category;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
use App\Shared\Infrastructure\Symfony\Messenger\QueryHandleTrait;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\Assertion;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminCreateCategoryController extends AbstractController
{
    use UserTrait;
    use QueryHandleTrait;
    use CommandHandleTrait;

    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        MessageBusInterface $commandBus,
        MessageBusInterface $queryBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/admin/gallery/categories/create", name="admin_gallery_category_create_post", methods={"POST"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        $categoryForm = [
            'name' => $request->request->get('name', null),
            'parent_id' => $request->request->getInt('parent_id', 0),
        ];

        $render = [];
        $category = null;

        try {
            $this->validate($categoryForm);
            /** @var Category $category */
            $category = $this->handleCommand(new CreateCategoryCommand(
                $categoryForm['name'],
                $categoryForm['parent_id'],
                true,
                $this->getUserId(),
                time()
            ));
            Assertion::notNull($category, 'admin.gallery.category.create.error.internal_error');
            $render['succeed'] = 'admin.gallery.category.create.confirm.category_created';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        } catch (\InvalidArgumentException $e) {
            $render['errors'] = [$e->getMessage()];
        }

        return $this->render('admin/gallery_category_create.html.twig', array_merge([
            'name' => $categoryForm['name'],
            'parent_id' => $categoryForm['parent_id'],
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
            'category_id' => $category ? $category->getId() : -1,
        ], $render));
    }

    /**
     * @Route("/admin/gallery/categories/create", name="admin_gallery_category_create", methods={"GET"})
     * @IsGranted("ROLE_PICTURES")
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::GALLERY)) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/gallery_category_create.html.twig', [
            'category_tree' => $this->handleQuery(new GetCategoriesTreeQuery()),
            'category_id' => -1,
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
