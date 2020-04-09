<?php

declare(strict_types=1);

namespace App\User\UI\Web\Controller;

use App\Shared\Domain\Criteria;
use App\User\Domain\Provider\UserDataProviderInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminListController extends AbstractController
{
    private UserDataProviderInterface $userDataProvider;

    public function __construct(
        UserDataProviderInterface $userDataProvider
    ) {
        $this->userDataProvider = $userDataProvider;
    }

    /**
     * @Route("/admin/users", name="admin_user_list", methods={"GET"})
     * @Route("/admin/users/{pageNumber}", name="admin_user_page_list", methods={"GET"})
     * @IsGranted("ROLE_USERS")
     */
    public function __invoke(int $pageNumber = 1): Response
    {
        $pagerfanta = new Pagerfanta(new class($this->userDataProvider) implements AdapterInterface {
            private UserDataProviderInterface $userDataProvider;

            public function __construct(UserDataProviderInterface $userDataProvider)
            {
                $this->userDataProvider = $userDataProvider;
            }

            public function getNbResults()
            {
                return $this->userDataProvider->countUsers();
            }

            public function getSlice($offset, $length)
            {
                return $this->userDataProvider->findUsers(
                    new Criteria(['updated_at' => 'DESC'], $offset, $length)
                );
            }
        });

        try {
            $pagerfanta
                ->setMaxPerPage(30)
                ->setCurrentPage($pageNumber)
            ;
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/user_list.html.twig', [
            'users' => $pagerfanta->getCurrentPageResults(),
            'page_number' => $pagerfanta->getNbPages(),
            'page_cur' => $pagerfanta->getCurrentPage(),
        ]);
    }
}
