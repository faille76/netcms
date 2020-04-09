<?php

declare(strict_types=1);

namespace App\Core\UI\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TermsController extends AbstractController
{
    /**
     * @Route("/terms", name="terms", methods={"GET"})
     */
    public function __invoke(): Response
    {
        return $this->render('terms/index.html.twig');
    }
}
