<?php

namespace App\Controller\Admin;

use App\Controller\Traits\ControllerTrait;
use App\Data\AdvertCrudData;
use App\Entity\Advert;
use App\Entity\AdvertPicture;
use App\Event\AdvertBadEvent;
use App\Event\AdvertInitEvent;
use App\Event\AdvertPreCreateEvent;
use App\Event\AdvertPreEditEvent;
use App\Form\AdvertType;
use App\Form\Filter\AdminAdvertFilterType;
use App\Manager\AdvertManager;
use App\Model\Admin\AdvertSearch;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Route('/admin')]
class AdvertController extends CrudController
{
    protected string $entity = Advert::class;
    protected string $templatePath = 'advert';
    protected string $routePrefix = 'app_admin_advert';
    protected string $createFlashMessage = 'Une annonce a été crée';
    protected string $editFlashMessage = 'Une annonce a été mise à jour';
    protected string $deleteFlashMessage = 'Une annonce a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les annonces ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les annonces n\'a pas pu être supprimé !';

    #[Route(path: '/adverts', name: 'app_admin_advert_index')]
    public function index(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertFilterType::class, $search);
        $form->handleRequest($request);

        $query = $this->getRepository()->getAdmins($search);

        return $this->crudIndex($query, $form, 1);
    }

    #[Route(path: '/adverts/{category_slug}/create', name: 'app_admin_advert_create')]
    public function create(
        Request $request,
        EventDispatcherInterface $dispatcher,
        AdvertManager $manager
    ): RedirectResponse|Response
    {
        $advert = $manager->createAdvert();
        $class_exist = class_exists($manager->createForm());

        $dispatcher->dispatch(new AdvertInitEvent($request));

        $form = $this->createForm($class_exist ? $manager->createForm() : AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dispatcher->dispatch(new AdvertPreCreateEvent($advert, $request));

            $advert->setCreatedAt(new DateTime());
            $advert->setUpdatedAt(new DateTime());

            $this->em->persist($advert);
            $this->em->flush();

            $this->addFlash('success', $this->createFlashMessage);

            return $this->redirectToRoute('app_admin_advert_index');
        } else {
            $dispatcher->dispatch(new AdvertBadEvent($advert, $request));
        }

        return $this->render('admin/'. $this->templatePath .'/create.html.twig', [
            'form' => $form->createView(),
            'view' => $class_exist ? $manager->viewRoute() : 'admin/advert/form.html.twig',
            'advert' => $advert
        ]);
    }

    #[Route(path: '/adverts/{category_slug}/{id}/edit', name: 'app_admin_advert_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        EventDispatcherInterface $dispatcher,
        AdvertManager $manager,
        Advert $advert
    ): RedirectResponse|Response
    {
        $dispatcher->dispatch(new AdvertInitEvent($request));
        $class_exist = class_exists($manager->editForm($advert));

        $form = $this->createForm($manager->editForm($advert), $advert);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dispatcher->dispatch(new AdvertPreEditEvent($advert, $request));

            $advert->setUpdatedAt(new DateTime());

            $this->em->flush();

            $this->addFlash('success', $this->editFlashMessage);

            return $this->redirectToRoute('app_admin_advert_index');
        } else {
            $dispatcher->dispatch(new AdvertBadEvent($advert, $request));
        }

        return $this->render('admin/'. $this->templatePath .'/edit.html.twig', [
            'form' => $form->createView(),
            'view' => $class_exist ? $manager->viewEditRoute($advert) : 'admin/advert/form.html.twig',
            'advert' => $advert
        ]);
    }

    #[Route(path: '/adverts/images/delete', name: 'app_admin_advert_image_delete')]
    public function deleteImage(Request $request): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        if (!$request->query->has('id')) {
            return $this->createNotFoundException('Bad request');
        }

        $picture = $this->em->getRepository(AdvertPicture::class)->find($request->query->get('id'));

        $picture->setAdvert(null);

        $this->em->remove($picture);
        $this->em->flush();

        return new JsonResponse(['success' => true, 'id' => null]);
    }

    #[Route(path: '/adverts/{id}/show', name: 'app_admin_advert_show', requirements: ['id' => '\d+'])]
    public function show(Advert $advert): Response
    {
        return $this->render('admin/advert/show.html.twig', ['advert' => $advert]);
    }

    #[Route(path: '/adverts/{id}/move', name: 'app_admin_advert_move', requirements: ['id' => '\d+'])]
    public function move(Advert $advert): RedirectResponse
    {
        return $this->crudMove($advert);
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

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette annonce ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' annonces ?';
    }
}
