<?php

declare(strict_types=1);

namespace App\Partner\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Partner\Application\Command\UpdatePartnerCommand;
use App\Partner\Domain\Provider\PartnerDataProviderInterface;
use App\Shared\UI\Web\Controller\UserTrait;
use Assert\Assert;
use Assert\LazyAssertionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUpdatePartnerController extends AbstractController
{
    use UserTrait;

    private FeatureFlippingInterface $featureFlipping;
    private PartnerDataProviderInterface $partnerDataProvider;
    private MessageBusInterface $commandBus;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        PartnerDataProviderInterface $partnerDataProvider,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->partnerDataProvider = $partnerDataProvider;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/partners/{partnerId}/update", name="admin_partner_update_post", methods={"POST"})
     * @IsGranted("ROLE_PARTNERS")
     */
    public function post(Request $request, int $partnerId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PARTNER)) {
            throw $this->createNotFoundException();
        }

        $partner = $this->partnerDataProvider->getPartnerById($partnerId);
        if ($partner === null) {
            throw $this->createNotFoundException();
        }

        $partnerForm = [
            'name' => $request->request->get('name', null),
            'image' => $request->files->get('image', null),
            'url' => $request->request->get('url', null),
        ];

        $render = [];

        try {
            $this->validate($partnerForm);
            $this->commandBus->dispatch(new UpdatePartnerCommand(
                $partner->getId(),
                $partnerForm['name'],
                $partnerForm['url'],
                $partnerForm['image'],
                $this->getUserId(),
                time()
            ));
            $render['succeed'] = 'admin.partners.create.confirm.partner_updated';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        }

        return $this->render('admin/partner_create.html.twig', array_merge([
            'name' => $partnerForm['name'],
            'image' => $partnerForm['image'],
            'url' => $partnerForm['url'],
        ], $render));
    }

    /**
     * @Route("/admin/partners/{partnerId}/update", name="admin_partner_update", methods={"GET"})
     * @IsGranted("ROLE_PARTNERS")
     */
    public function __invoke(int $partnerId): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PARTNER)) {
            throw $this->createNotFoundException();
        }

        $partner = $this->partnerDataProvider->getPartnerById($partnerId);
        if ($partner === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/partner_create.html.twig', [
            'name' => $partner->getName(),
            'image' => $partner->getImage(),
            'url' => $partner->getUrl(),
        ]);
    }

    /**
     * @throws LazyAssertionException
     */
    private function validate(array $partner): void
    {
        $lazyAssert = Assert::lazy();
        $lazyAssert
            ->that($partner['name'])
            ->notEmpty('admin.partners.create.error.name_empty')
            ->that($partner['url'])
            ->notEmpty('admin.partners.create.error.url_empty')
            ->url('admin.partners.create.error.invalid_url')
        ;
        if ($partner['image'] !== null) {
            $lazyAssert->that($partner['image']->isValid())->true('admin.partners.create.error.invalid_image');
            $lazyAssert->that($partner['image']->getMimeType())->inArray(MimeTypeEnum::toArray(), 'admin.partners.create.error.invalid_image');
        }
        $lazyAssert->verifyNow();
    }
}
