<?php

declare(strict_types=1);

namespace App\Document\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Document\Application\Command\UpdateDocumentCommand;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdateDocumentController extends AbstractController
{
    use UserTrait;

    private DocumentDataProviderInterface $documentDataProvider;
    private MessageBusInterface $commandBus;
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
     * @Route("/admin/documents/{documentId}/update", name="admin_document_update_post", methods={"POST"})
     * @IsGranted("ROLE_UPLOAD")
     */
    public function post(Request $request, int $documentId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::DOCUMENT)) {
            throw $this->createNotFoundException();
        }

        $document = $this->documentDataProvider->getDocument($documentId);
        if ($document === null) {
            throw $this->createNotFoundException();
        }

        $documentForm = [
            'name' => $request->request->get('name', null),
        ];

        $render = [];

        try {
            $this->validate($documentForm);
            $this->commandBus->dispatch(new UpdateDocumentCommand(
                $document->getId(),
                $documentForm['name'],
                $this->getUserId(),
                time()
            ));
            $render['succeed'] = 'admin.documents.create.confirm.document_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/document_create.html.twig', array_merge([
            'name' => $documentForm['name'],
            'fileName' => $document->getFileName(),
        ], $render));
    }

    /**
     * @Route("/admin/documents/{documentId}/update", name="admin_document_update", methods={"GET"})
     * @IsGranted("ROLE_UPLOAD")
     */
    public function __invoke(Request $request, int $documentId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::DOCUMENT)) {
            throw $this->createNotFoundException();
        }

        $document = $this->documentDataProvider->getDocument($documentId);
        if ($document === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/documents_create.html.twig', [
            'name' => $document->getName(),
            'file' => $document->getFileName(),
        ]);
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
        ;
        $lazyAssert->verifyNow();
    }
}
