<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class PagesController extends AbstractController
{
    use ControllerTrait;

    public function __construct(private Breadcrumbs $breadcrumbs)
    {
    }

    #[Route(path: '/a-propos', name: 'app_env')]
    public function env(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('À propos');

        return $this->render('site/pages/env.html.twig');
    }

    #[Route(path: '/equipments', name: 'app_equipment')]
    public function equipment(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Équipements');

        return $this->render('site/pages/equipment.html.twig');
    }
}



