<?php

namespace App\Controller\Admin;

use App\Repository\AchieveRepository;
use App\Repository\PostRepository;
use App\Repository\ReviewRepository;
use App\Repository\ServiceRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private AchieveRepository $achieveRepository,
        private ReviewRepository $reviewRepository,
        private TeamRepository $teamRepository,
        private ServiceRepository $serviceRepository,
        private PostRepository $postRepository
    )
    {
    }

    #[Route(path: '/admin', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'service' => $this->serviceRepository->getNumber(),
            'achieve' => $this->achieveRepository->getNumber(),
            'review'  => $this->reviewRepository->getNumber(),
            'team'    => $this->teamRepository->getNumber(),
            'post'    => $this->postRepository->getNumber()
        ]);
    }
}
