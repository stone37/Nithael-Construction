<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class PagesController extends AbstractController
{
    use ControllerTrait;

    public function __construct(private readonly Breadcrumbs $breadcrumbs)
    {
    }

    #[Route(path: '/nos-services', name: 'app_service')]
    public function service(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Nos services');

        return $this->render('site/pages/service.html.twig');
    }

    #[Route(path: '/a-propos', name: 'app_env')]
    public function env(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('À propos');

        return $this->render('site/pages/env.html.twig');
    }
}



