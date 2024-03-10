<?php

namespace App\Controller;

use App\Repository\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReferenceController extends AbstractController
{
    public function __construct(private ReferenceRepository $repository)
    {
    }

    public function index(): Response
    {
        return $this->render('site/reference/index.html.twig', ['references' => $this->repository->queryAll()]);
    }
}

