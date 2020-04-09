<?php

declare(strict_types=1);

namespace App\Document\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Document\Application\Command\CreateDocumentCommand;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Shared\Infrastructure\Symfony\Messenger\CommandHandleTrait;
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

final class AdminCreateDocumentController extends AbstractController
{
    use CommandHandleTrait;
    use UserTrait;

    private DocumentDataProviderInterface $documentDataProvider;
    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        DocumentDataProviderInterface $documentDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->documentDataProvider = $documentDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/documents/create", name="admin_document_create_post", methods={"POST"})
     * @IsGranted("ROLE_UPLOAD")
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::DOCUMENT)) {
            throw $this->createNotFoundException();
        }

        $documentForm = [
            'name' => $request->request->get('name', null),
            'file' => $request->files->get('file', null),
        ];

        $render = [];

        try {
            $this->validate($documentForm);
            $document = $this->handleCommand(new CreateDocumentCommand(
                $documentForm['name'],
                $documentForm['file'],
                true,
                $this->getUserId(),
                time()
            ));
            Assertion::notNull($document, 'admin.documents.create.error.internal_error');
            $render['succeed'] = 'admin.documents.create.confirm.document_created';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        } catch (\InvalidArgumentException $e) {
            $render['errors'] = [$e->getMessage()];
        }

        return $this->render('admin/documents_create.html.twig', array_merge([
            'name' => $documentForm['name'],
        ], $render));
    }

    /**
     * @Route("/admin/documents/create", name="admin_document_create", methods={"GET"})
     * @IsGranted("ROLE_UPLOAD")
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::DOCUMENT)) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/documents_create.html.twig');
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $document): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($document['name'])
            ->notEmpty('admin.documents.create.error.name_empty')
            ->that($document['file'])
            ->notNull('admin.documents.create.error.invalid_file')
        ;
        if ($document['file'] !== null) {
            $lazyAssert->that($document['file']->isValid())->true('admin.documents.create.error.invalid_file');
        }
        $lazyAssert->verifyNow();
    }
}
