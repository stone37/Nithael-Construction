<?php

namespace App\Controller\Admin;

use App\Data\CarouselItemCrudData;
use App\Entity\CarouselItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CarouselItemController extends CrudController
{
    protected string $entity = CarouselItem::class;
    protected string $templatePath = 'carousel';
    protected string $routePrefix = 'app_admin_carousel';
    protected string $createFlashMessage = 'Un carousel a été crée';
    protected string $editFlashMessage = 'Un carousel a été mise à jour';
    protected string $deleteFlashMessage = 'Le carousel a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les carousels ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les carousels n\'a pas pu être supprimé !';

    #[Route(path: '/carousels', name: 'app_admin_carousel_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/carousels/create', name: 'app_admin_carousel_create')]
    public function create(): Response
    {
        $entity = new CarouselItem();
        $data = new CarouselItemCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/carousels/{id}/edit', name: 'app_admin_carousel_edit', requirements: ['id' => '\d+'])]
    public function edit(CarouselItem $item): Response
    {
        $data = new CarouselItemCrudData($item);

        return $this->crudEdit($data);
    }

    #[Route(path: '/carousels/{id}/move', name: 'app_admin_carousel_move', requirements: ['id' => '\d+'])]
    public function move(CarouselItem $item): RedirectResponse
    {
        return $this->crudMove($item);
    }

    #[Route(path: '/carousels/{id}/delete', name: 'app_admin_carousel_delete', requirements: ['id' => '\d+'])]
    public function delete(CarouselItem $item): RedirectResponse|JsonResponse
    {
        $data = new CarouselItemCrudData($item);

        return $this->crudDelete($data);
    }

    #[Route(path: '/carousels/bulk/delete', name: 'app_admin_carousel_bulk_delete')]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet carousel ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' carousels ?';
    }
}
