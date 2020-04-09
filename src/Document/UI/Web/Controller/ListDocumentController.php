<?php

declare(strict_types=1);

namespace App\Document\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\Document\Domain\Document;
use App\Document\Domain\Provider\DocumentDataProviderInterface;
use App\Shared\Domain\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ListDocumentController extends AbstractController
{
    private FeatureFlippingInterface $featureFlipping;
    private DocumentDataProviderInterface $documentDataProvider;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        DocumentDataProviderInterface $documentDataProvider
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->documentDataProvider = $documentDataProvider;
    }

    /**
     * @Route("/documents", name="documents", methods={"GET"})
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::DOCUMENT)) {
            throw $this->createNotFoundException();
        }

        return $this->render('documents/index.html.twig', [
            'documents' => $this->getDocuments(),
        ]);
    }

    private function getDocuments(): array
    {
        $documentList = [];
        $documentCollection = $this->documentDataProvider->findDocuments(true, new Criteria(['id' => 'DESC']));
        /** @var Document $document */
        foreach ($documentCollection as $document) {
            $images = ['png', 'gif', 'jpg', 'jpeg', 'PNG', 'GIF', 'JPG', 'JPEG'];
            $word = ['docx', 'doc', 'odt'];
            $excel = ['xlsx', 'xls', 'xlt', 'xla', 'ods'];
            if (in_array($document->getType(), $images)) {
                $format = 'pics.gif';
            } elseif (in_array($document->getType(), $word)) {
                $format = 'word.gif';
            } elseif (in_array($document->getType(), $excel)) {
                $format = 'excel.gif';
            } elseif ($document->getType() == 'pdf') {
                $format = 'pdf1.gif';
            } else {
                $format = 'document.gif';
            }

            $documentList[] = [
                'pic' => $format,
                'type' => $document->getType(),
                'name' => $document->getName(),
                'author' => $document->getAuthor(),
                'createdAt' => $document->getCreatedAt(),
                'fileName' => $document->getFileName(),
            ];
        }

        return $documentList;
    }
}
