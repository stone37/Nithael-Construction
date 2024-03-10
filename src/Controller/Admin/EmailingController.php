<?php

namespace App\Controller\Admin;

use App\Data\EmailingCrudData;
use App\Entity\Emailing;
use App\Form\EmailingSenderType;
use App\Form\EmailingType;
use App\Repository\NewsletterDataRepository;
use App\Repository\UserRepository;
use App\Service\NewsletterService;;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class EmailingController extends CrudController
{
    protected string $entity = Emailing::class;
    protected string $templatePath = 'emailing';
    protected string $routePrefix = 'app_admin_emailing';
    protected string $deleteFlashMessage = 'Un mail a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les mails ont été supprimé';
    protected string $deleteErrorFlashMessage = 'Désolé, les mails n\'a pas pu être supprimée !';

    private string $sendFlashMessage = 'Votre email a été envoyé';
    private string $newsletterSendFlashMessage = 'Votre newsletter a été envoyée avec succès';
    private string $newsletterNotSenderFlashMessage = 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire';

    #[Route(path: '/emailing/{type}/list', name: 'app_admin_emailing_index', requirements: ['type' => '\d+'])]
    public function index(int $type): Response
    {
        $query = $this->getRepository()->getAdmins($type);

        return $this->crudIndex($query, null, $type);
    }

    #[Route(path: '/emailing/create', name: 'app_admin_emailing_create')]
    public function create(Request $request, NewsletterService $newsletter): RedirectResponse|Response
    {
        $emailing = new Emailing();

        $form = $this->createForm(EmailingSenderType::class, $emailing);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getRepository()->add($emailing, true);

            $newsletter->sendEmailing($emailing);

            $this->addFlash('success', $this->sendFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 1]);
        }

        return $this->render('admin/' . $this->templatePath . '/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/emailing/{id}/resend/{type}', name: 'app_admin_emailing_resend', requirements: ['id' => '\d+'])]
    public function resend(
        Request $request,
        UserRepository $userRepository,
        NewsletterDataRepository $newsletterDataRepository,
        NewsletterService $newsletter,
        Emailing $emailing,
        string $type
    ): RedirectResponse|Response
    {
        if ($emailing->getGroupe() == Emailing::GROUP_PARTICULIER) {
            $form = $this->createForm(EmailingSenderType::class, $emailing);
        } else {
            $form = $this->createForm(EmailingType::class, $emailing);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($emailing->getGroupe() == Emailing::GROUP_PARTICULIER) {

                $this->getRepository()->flush();
                $newsletter->sendEmailing($emailing);
                $this->addFlash('success', $this->sendFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 1]);

            } elseif ($emailing->getGroupe() == Emailing::GROUP_USER) {
                $users = $userRepository->findAllUsers();

                if (empty($users)) {
                    $this->addFlash('error', $this->newsletterNotSenderFlashMessage);

                    return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 2]);
                }

                $this->getRepository()->flush();
                $newsletter->sendUserEmailing($emailing, $users);
                $this->addFlash('success', $this->newsletterSendFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 2]);

            } elseif ($emailing->getGroupe() == Emailing::GROUP_USER_PRO) {
                $users = $userRepository->findAllProUsers();

                if (empty($users)) {
                    $this->addFlash('error', $this->newsletterNotSenderFlashMessage);

                    return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 3]);
                }

                $this->getRepository()->flush();
                $newsletter->sendProUserEmailing($emailing, $users);
                $this->addFlash('success', $this->newsletterSendFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 3]);
            } else {
                $newsletters = $newsletterDataRepository->findAll();

                if (empty($newsletters)) {
                    $this->addFlash('error', $this->newsletterNotSenderFlashMessage);

                    return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 4]);
                }

                $this->getRepository()->flush();

                $newsletter->sendNewsletterEmailing($emailing, $newsletters);

                $this->addFlash('success', $this->newsletterSendFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 4]);
            }
        }

        return $this->render('admin/' . $this->templatePath . '/edit.html.twig', [
            'form' => $form->createView(),
            'emailing' => $emailing,
            'type' => $type,
        ]);
    }

    #[Route(path: '/emailing/create/user', name: 'app_admin_emailing_user')]
    public function user(Request $request, UserRepository $userRepository, NewsletterService $newsletter): Response
    {
        $users = $userRepository->findAllUsers();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_USER);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($users)) {
                $this->addFlash('error', $this->newsletterNotSenderFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 3]);
            }

            $this->getRepository()->add($emailing, true);

            $newsletter->sendUserEmailing($emailing, $users);

            $this->addFlash('success', $this->newsletterSendFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 3]);
        }

        return $this->render('admin/'. $this->templatePath .'/user.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route(path: '/emailing/create/user-pro', name: 'app_admin_emailing_user_pro')]
    public function userPro(Request $request, UserRepository $userRepository, NewsletterService $newsletter): Response
    {
        $users = $userRepository->findAllProUsers();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_USER_PRO);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($users)) {
                $this->addFlash('error', $this->newsletterNotSenderFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 2]);
            }

            $this->getRepository()->add($emailing, true);

            $newsletter->sendProUserEmailing($emailing, $users);

            $this->addFlash('success', $this->newsletterSendFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 2]);
        }

        return $this->render('admin/'. $this->templatePath .'/user-pro.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route(path: '/emailing/create/newsletter', name: 'app_admin_emailing_newsletter')]
    public function newsletter(Request $request, NewsletterDataRepository $newsletterDataRepository, NewsletterService $newsletter): RedirectResponse|Response
    {
        $newsletters = $newsletterDataRepository->findAll();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_NEWSLETTER);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($newsletters)) {
                $this->addFlash('error', $this->newsletterNotSenderFlashMessage);

                return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 4]);
            }

            $this->getRepository()->add($emailing, true);

            $newsletter->sendNewsletterEmailing($emailing, $newsletters);

            $this->addFlash('success', $this->newsletterSendFlashMessage);

            return $this->redirectToRoute($this->routePrefix . '_index', ['type' => 4]);
        }

        return $this->render('admin/'. $this->templatePath .'/newsletter.html.twig', [
            'form' => $form->createView(),
            'newsletters' => $newsletters,
        ]);
    }

    #[Route(path: '/emailing/{id}/delete', name: 'app_admin_emailing_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Emailing $emailing): RedirectResponse|JsonResponse
    {
        $data = new EmailingCrudData($emailing);

        return $this->crudDelete($data);
    }

    #[Route(path: '/emailing/bulk/delete', name: 'app_admin_emailing_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet mail ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' mails ?';
    }
}


