<?php

namespace App\Controller;

use App\Repository\CarouselItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CarouselItemController extends AbstractController
{
    public function __construct(private readonly CarouselItemRepository $repository)
    {
    }

    public function index(): Response
    {
        return $this->render('site/layout/_home_carousel.html.twig', ['carouselItems' => $this->repository->queryAll()]);
    }
}

