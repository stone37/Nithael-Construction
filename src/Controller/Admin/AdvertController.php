<?php

namespace App\Controller\Admin;

use App\Data\AdvertCrudData;
use App\Entity\Advert;
use App\Entity\User;
use App\Event\AdvertDeniedEvent;
use App\Event\AdvertValidatedEvent;
use App\Form\Filter\AdminAdvertType;
use App\Manager\AdvertManager;
use App\Model\Admin\AdvertSearch;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdvertController extends CrudController
{
    protected string $entity = Advert::class;
    protected string $templatePath = 'advert';
    protected string $routePrefix = 'app_admin_advert';
    protected string $deleteFlashMessage = 'Une annonce a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les annonces ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les annonces n\'a pas pu être supprimé !';

    #[Route(path: '/adverts', name: 'app_admin_advert_index')]
    public function index(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdmins($search);

        return $this->crudIndex($query, $form, 1);
    }

    #[Route(path: '/adverts/validated', name: 'app_admin_advert_validated_index')]
    public function validate(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getValidateAdmins($search);

        return $this->crudIndex($query, $form, 2);
    }

    #[Route(path: '/adverts/refused', name: 'app_admin_advert_refused_index')]
    public function refused(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getDeniedAdmins($search);

        return $this->crudIndex($query, $form, 3);
    }

    #[Route(path: '/adverts/archive', name: 'app_admin_advert_archive_index')]
    public function archive(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getArchiveAdmins($search);

        return $this->crudIndex($query, $form, 4);
    }

    #[Route(path: '/adverts/remove', name: 'app_admin_advert_remove_index')]
    public function removed(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getRemoveAdmins($search);

        return $this->crudIndex($query, $form, 5);
    }

    #[Route(path: '/adverts/{id}/user', name: 'app_admin_advert_user', requirements: ['id' => '\d+'])]
    public function user(Request $request, User $user): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdminByUser($user, $search);

        return $this->crudIndex($query, $form, 6);
    }

    #[Route(path: '/adverts/{id}/show/{type}', name: 'app_admin_advert_show', requirements: ['id' => '\d+'])]
    public function show(Advert $advert, $type): Response
    {
        return $this->render('admin/advert/show.html.twig', ['advert' => $advert, 'type' => $type]);
    }

    #[Route(path: '/adverts/{id}/validate', name: 'app_admin_advert_validate', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function validated(Request $request, AdvertManager $manager, Advert $advert): RedirectResponse|JsonResponse
    {
        $form = $this->validateForm($advert);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $manager->validate($advert);

                $this->getRepository()->flush();

                $this->dispatcher->dispatch(new AdvertValidatedEvent($advert));

                $this->addFlash('success', 'L\'annonce a été valider');
            } else {
                $this->addFlash('error', 'Désolé, l\'annonce n\'a pas pu être valider !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir valider cette annonce ?';

        $render = $this->render('ui/modal/_validate.html.twig', [
            'form' => $form->createView(),
            'data' => $advert,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/bulk/validate', name: 'app_admin_advert_bulk_validate', options: ['expose' => true])]
    public function validatedBulk(Request $request, AdvertManager $manager): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $ids);
        }

        $form = $this->validateMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $advert = $this->getRepository()->find($id);

                    $manager->validate($advert);

                    $this->getRepository()->flush();

                    $this->dispatcher->dispatch(new AdvertValidatedEvent($advert));
                }

                $this->addFlash('success', 'Les annonces ont été valider');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être valider !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir valider ces '.count($ids).' annonces ?';
        else
            $message = 'Être vous sur de vouloir valider cette annonce ?';

        $render = $this->render('ui/modal/_validate_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/{id}/denied', name: 'app_admin_advert_denied', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function denied(Request $request, AdvertManager $manager, Advert $advert): RedirectResponse|JsonResponse
    {
        $form = $this->deniedForm($advert);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $manager->denied($advert);

                $this->getRepository()->flush();

                $this->dispatcher->dispatch(new AdvertDeniedEvent($advert));

                $this->addFlash('success', 'L\'annonce a été refuser');
            } else {
                $this->addFlash('error', 'Désolé, l\'annonce n\'a pas pu être refuser !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir refuser cette annonce ?';

        $render = $this->render('ui/modal/_denied.html.twig', [
            'form' => $form->createView(),
            'data' => $advert,
            'message' => $message,
            'configuration' => $this->getConfiguration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/bulk/denied', name: 'app_admin_advert_bulk_denied', options: ['expose' => true])]
    public function deniedBulk(Request $request, AdvertManager $manager): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $ids);
        }

        $form = $this->deniedMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $advert = $this->getRepository()->find($id);

                    $manager->denied($advert);

                    $this->getRepository()->flush();

                    $this->dispatcher->dispatch(new AdvertDeniedEvent($advert));
                }

                $this->addFlash('success', 'Les annonces ont été refuser');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être refuser !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir refuser ces '.count($ids).' annonces ?';
        else
            $message = 'Être vous sur de vouloir refuser cette annonce ?';

        $render = $this->render('ui/modal/_denied_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->getConfiguration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/{id}/delete', name: 'app_admin_advert_delete', requirements: ['id' => '\d+'])]
    public function delete(Advert $advert): RedirectResponse|JsonResponse
    {
        $data = new AdvertCrudData($advert);

        return $this->crudDelete($data);
    }

    #[Route(path: '/adverts/bulk/delete', name: 'app_admin_advert_bulk_delete')]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    #[Route(path: '/adverts/{type}/clean', name: 'app_admin_advert_clean', requirements: ['type' => '\d+'])]
    public function clean(Request $request, int $type): NotFoundHttpException|RedirectResponse|JsonResponse
    {
        if ($type === 1) {
            $adverts = $this->getRepository()->deniedClean();
        } elseif ($type === 2) {
            $adverts = $this->getRepository()->archivedClean();
        } elseif ($type === 3) {
            $adverts = $this->getRepository()->removedClean();
        } else {
            return $this->createNotFoundException('Bad request');
        }

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_clean', ['type' => $type]))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if ($type === 1) {
                    $this->getRepository()->deniedClean(true);
                } elseif ($type === 2) {
                    $this->getRepository()->archivedClean(true);
                } elseif ($type === 3) {
                    $this->getRepository()->removedClean(true);
                }

                $this->addFlash('success', 'Les annonces ont été supprimer');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être refuser !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($adverts) > 0) {
            if (count($adverts) > 1) {
                $message = 'Être vous sur de vouloir supprimer ces '. count($adverts) .' annonces ?';
            } else {
                $message = 'Être vous sur de vouloir supprimer cette annonce ?';
            }

            $render = $this->render('ui/modal/_clean.html.twig', [
                'form' => $form->createView(),
                'data' => $adverts,
                'message' => $message,
                'configuration' => $this->getConfiguration()
            ]);

            $response['html'] = $render->getContent();

            return new JsonResponse($response);
        } else {
            return $this->createNotFoundException('Bad request');
        }
    }

    #[Route(path: '/adverts/reload', name: 'app_admin_advert_reload')]
    public function reload(Request $request,): NotFoundHttpException|RedirectResponse|JsonResponse
    {
        $adverts = $this->getRepository()->archivedClean();

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_reload'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Advert $advert */
                foreach ($adverts as $advert) {
                    $advert->setValidatedAt(new DateTime());
                }

                $this->getRepository()->flush();

                $this->addFlash('success', 'Les annonces ont été relancée');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être relancée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($adverts) > 0) {
            if (count($adverts) > 1) {
                $message = 'Être vous sur de vouloir relancer ces '.count($adverts).' annonces ?';
            } else {
                $message = 'Être vous sur de vouloir relancer cette annonce ?';
            }

            $render = $this->render('ui/modal/_reload.html.twig', [
                'form' => $form->createView(),
                'data' => $adverts,
                'message' => $message,
                'configuration' => $this->getConfiguration(),
            ]);

            $response['html'] = $render->getContent();
            $response['status'] = true;

            return new JsonResponse($response);
        } else {
            return $this->createNotFoundException('Bad request');
        }
    }


    private function validateForm(Advert $advert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_validate', ['id' => $advert->getId()]))
            ->getForm();
    }

    private function validateMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_bulk_validate'))
            ->getForm();
    }

    private function deniedForm(Advert $advert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_denied', ['id' => $advert->getId()]))
            ->getForm();
    }

    private function deniedMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_bulk_denied'))
            ->getForm();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette annonce ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' annonces ?';
    }

    #[ArrayShape(['modal' => "\string[][]"])] protected function getConfiguration(): array
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ],
                'validate' => [
                    'type' => 'modal-success',
                    'icon' => 'fas fa-reply',
                    'yes_class' => 'btn-outline-success',
                    'no_class' => 'btn-success'
                ],
                'denied' => [
                    'type' => 'modal-amber',
                    'icon' => 'fas fa-share',
                    'yes_class' => 'btn-outline-amber',
                    'no_class' => 'btn-amber'
                ],
                'reload' => [
                    'type' => 'modal-secondary',
                    'icon' => 'fas fa-check',
                    'yes_class' => 'btn-outline-secondary',
                    'no_class' => 'btn-secondary'
                ]

            ]
        ];
    }
}
