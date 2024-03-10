<?php

namespace App\Controller\Admin;

use App\Data\AdvertCategoryCrudData;
use App\Entity\AdvertCategory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdvertCategoryController extends CrudController
{
    protected string $entity = AdvertCategory::class;
    protected string $templatePath = 'advert-category';
    protected string $routePrefix = 'app_admin_advert_category';
    protected string $createFlashMessage = 'Une catégorie a été crée';
    protected string $editFlashMessage = 'Une catégorie a été mise à jour';
    protected string $deleteFlashMessage = 'La catégorie a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les catégories ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les catégories n\'a pas pu être supprimé !';

    #[Route(path: '/advert-categories', name: 'app_admin_advert_category_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/advert-categories/create', name: 'app_admin_advert_category_create')]
    public function create(): Response
    {
        $entity = new AdvertCategory();
        $data = new AdvertCategoryCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/advert-categories/{id}/edit', name: 'app_admin_advert_category_edit', requirements: ['id' => '\d+'])]
    public function edit(AdvertCategory $category): Response
    {
        $data = new AdvertCategoryCrudData($category);

        return $this->crudEdit($data);
    }

    #[Route(path: '/advert-categories/{id}/move', name: 'app_admin_advert_category_move', requirements: ['id' => '\d+'])]
    public function move(AdvertCategory $category): RedirectResponse
    {
        return $this->crudMove($category);
    }

    #[Route(path: '/advert-categories/{id}/delete', name: 'app_admin_advert_category_delete', requirements: ['id' => '\d+'])]
    public function delete(AdvertCategory $category): RedirectResponse|JsonResponse
    {
        $data = new AdvertCategoryCrudData($category);

        return $this->crudDelete($data);
    }

    #[Route(path: '/advert-categories/bulk/delete', name: 'app_admin_advert_category_bulk_delete')]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette catégorie ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' catégories ?';
    }
}
