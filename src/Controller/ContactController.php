<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Data\ContactData;
use App\Exception\TooManyContactException;
use App\Form\ContactType;
use App\Service\ContactService;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ContactController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly Breadcrumbs    $breadcrumbs,
        private readonly ContactService $contactService,
        private readonly ReCaptcha $reCaptcha
    )
    { 
    }

    #[Route(path: '/contact', name: 'app_contact_index')]
    public function index(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Contact');

        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()) {
                try {
                    $this->contactService->send($data, $request);
                } catch (TooManyContactException) {
                    $this->addFlash('error', 'Vous avez fait trop de demandes de contact consécutives.');

                    return $this->redirectToRoute('app_contact_index');
                }

                $this->addFlash('success', 'Votre demande de contact a été transmis, nous vous répondrons dans les meilleurs délais.');

            } else {
                $this->addFlash('error', 'Erreur pendant l\'envoi de votre message');

            }
            return $this->redirectToRoute('app_contact_index');
        }

        return $this->render('site/contact/index.html.twig', ['form' => $form->createView()]);
    }
}

