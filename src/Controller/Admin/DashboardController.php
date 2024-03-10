<?php

namespace App\Controller\Admin;

use App\Controller\Traits\ControllerTrait;
use App\Mailing\Mailer;
use App\Repository\AdvertRepository;
use App\Repository\NewsletterDataRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DashboardController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly AdvertRepository $advertRepository,
        private readonly PostRepository $postRepository,
        private readonly NewsletterDataRepository $newsletterDataRepository
    )
    {
    }

    #[Route(path: '/admin', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'advertEnabled' => $this->advertRepository->getEnabledNumber(),
            'advert' => $this->advertRepository->getDisabledNumber(),
            'newsletterData' => $this->newsletterDataRepository->getNumber(),
            'post'    => $this->postRepository->getNumber()
        ]);
    }

    /**
     * Envoie un email de test à mail-tester pour vérifier la configuration du serveur.
     * @param Request $request
     * @param Mailer $mailer
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route(path: '/admin/mailtester', name: 'app_admin_mailtest', methods: ['POST'])]
    public function testMail(Request $request, Mailer $mailer): RedirectResponse
    {
        $email = $mailer->createEmail('mails/auth/register.twig', ['user' => $this->getUserOrThrow(),])
            ->to($request->get('email'))
            ->subject('Hotel particulier | Confirmation du compte');

        $mailer->send($email);

        $this->addFlash('success', "L'email de test a bien été envoyé");

        return $this->redirectToRoute('app_admin_index');
    }
}
