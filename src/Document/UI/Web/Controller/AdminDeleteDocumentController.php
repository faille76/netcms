<?php

declare(strict_types=1);

namespace App\Document\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Document\Application\Command\DeleteDocumentCommand;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeleteDocumentController extends AbstractController
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
     * @Route("/admin/documents/{documentId}/delete", name="admin_document_delete", methods={"GET"})
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

        $this->commandBus->dispatch(new DeleteDocumentCommand(
            $document->getId(),
            $this->getUserId(),
            time()
        ));

        return $this->redirectToRoute('admin_document_list.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
