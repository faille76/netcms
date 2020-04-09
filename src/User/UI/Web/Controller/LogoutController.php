<?php

declare(strict_types=1);

namespace App\User\UI\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LogoutController extends AbstractController
{
    /**
     * @Route("/logout", name="user_logout", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $request->getSession()->clear();

        return $this->redirectToRoute('home.lang', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
