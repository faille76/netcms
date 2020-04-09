<?php

declare(strict_types=1);

namespace App\Document\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Shared\Domain\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListDocumentController extends AbstractController
{
    private DocumentDataProviderInterface $documentDataProvider;
    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        DocumentDataProviderInterface $documentDataProvider
    ) {
        $this->documentDataProvider = $documentDataProvider;
        $this->featureFlipping = $featureFlipping;
    }

    /**
     * @Route("/admin/documents", name="admin_document_list", methods={"GET"})
     * @IsGranted("ROLE_UPLOAD")
     */
    public function __invoke(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::DOCUMENT)) {
            throw $this->createNotFoundException();
        }

        $documents = $this->documentDataProvider->findDocuments(null, new Criteria(['id' => 'DESC']));

        return $this->render('admin/documents_list.html.twig', [
            'documents' => $documents,
        ]);
    }
}
