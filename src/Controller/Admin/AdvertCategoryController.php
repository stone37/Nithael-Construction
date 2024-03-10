<?php

namespace App\Controller\Admin;

use App\Data\CategoryCrudData;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CategoryController extends CrudController
{
    protected string $entity = Category::class;
    protected string $templatePath = 'category';
    protected string $routePrefix = 'app_admin_category';
    protected string $createFlashMessage = 'Une catégorie a été crée';
    protected string $editFlashMessage = 'Une catégorie a été mise à jour';
    protected string $deleteFlashMessage = 'La catégorie a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les catégories ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les catégories n\'a pas pu être supprimé !';

    #[Route(path: '/categories', name: 'app_admin_category_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/categories/create', name: 'app_admin_category_create')]
    public function create(): Response
    {
        $entity = new Category();
        $data = new CategoryCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/categories/{id}/edit', name: 'app_admin_category_edit', requirements: ['id' => '\d+'])]
    public function edit(Category $category): Response
    {
        $data = new CategoryCrudData($category);

        return $this->crudEdit($data);
    }

    #[Route(path: '/categories/{id}/move', name: 'app_admin_category_move', requirements: ['id' => '\d+'])]
    public function move(Category $category): RedirectResponse
    {
        return $this->crudMove($category);
    }

    #[Route(path: '/categories/{id}/delete', name: 'app_admin_category_delete', requirements: ['id' => '\d+'])]
    public function delete(Category $category): RedirectResponse|JsonResponse
    {
        $data = new CategoryCrudData($category);

        return $this->crudDelete($data);
    }

    #[Route(path: '/categories/bulk/delete', name: 'app_admin_category_bulk_delete')]
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
