<?php

declare(strict_types=1);

namespace App\Partner\UI\Web\Controller;

use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;
use App\ImageFactory\Domain\MimeTypeEnum;
use App\Partner\Application\Command\CreatePartnerCommand;
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

final class AdminCreatePartnerController extends AbstractController
{
    use UserTrait;
    use CommandHandleTrait;

    private FeatureFlippingInterface $featureFlipping;

    public function __construct(
        FeatureFlippingInterface $featureFlipping,
        MessageBusInterface $commandBus
    ) {
        $this->featureFlipping = $featureFlipping;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/admin/partners/create", name="admin_partner_create_post", methods={"POST"})
     * @IsGranted("ROLE_PARTNERS")
     */
    public function post(Request $request): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PARTNER)) {
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
            $partner = $this->handleCommand(new CreatePartnerCommand(
                $partnerForm['name'],
                $partnerForm['url'],
                $partnerForm['image'],
                true,
                $this->getUserId(),
                time()
            ));
            Assertion::notNull($partner, 'admin.partners.create.error.internal_error');
            $render['succeed'] = 'admin.partners.create.confirm.partner_created';
        } catch (LazyAssertionException $lazyAssert) {
            $errorList = [];
            foreach ($lazyAssert->getErrorExceptions() as $e) {
                $errorList[] = $e->getMessage();
            }
            $render['errors'] = $errorList;
        } catch (\InvalidArgumentException $e) {
            $render['errors'] = [$e->getMessage()];
        }

        return $this->render('admin/partner_create.html.twig', array_merge([
            'name' => $partnerForm['name'],
            'url' => $partnerForm['url'],
        ], $render));
    }

    /**
     * @Route("/admin/partners/create", name="admin_partner_create", methods={"GET"})
     * @IsGranted("ROLE_PARTNERS")
     */
    public function __invoke(): Response
    {
        if (!$this->featureFlipping->isModuleEnabled(FeatureEnum::PARTNER)) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/partner_create.html.twig');
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
            ->that($partner['image'])
            ->notNull('admin.partners.create.error.image_empty')
        ;
        if ($partner['image'] !== null) {
            $lazyAssert->that($partner['image']->isValid())->true('admin.partners.create.error.invalid_image');
            $lazyAssert->that($partner['image']->getMimeType())->inArray(MimeTypeEnum::toArray(), 'admin.partners.create.error.invalid_image');
        }
        $lazyAssert->verifyNow();
    }
}
