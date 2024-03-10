<?php

namespace App\Controller\Admin;

use App\Data\AdminCrudData;
use App\Entity\Admin;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends CrudController
{
    protected string $entity = Admin::class;
    protected string $templatePath = 'admin';
    protected string $routePrefix = 'app_admin_admin';
    protected string $createFlashMessage = 'Un compte admin a été crée';
    protected string $editFlashMessage = 'Un compte admin a été mise à jour';
    protected string $deleteFlashMessage = 'Le compte admin a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les comptes admin ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, le admin n\'a pas pu être supprimée !';

    #[Route(path: '/admins', name: 'app_admin_admin_index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/admins/create', name: 'app_admin_admin_create')]
    public function create(UserPasswordHasherInterface $hasher): Response
    {
        $data = new AdminCrudData($hasher);
        $data->entity = new Admin();

        return $this->crudNew($data);
    }

    #[Route(path: '/admins/{id}/edit', name: 'app_admin_admin_edit', requirements: ['id' => '\d+'])]
    public function edit(Admin $admin, UserPasswordHasherInterface $hasher): Response
    {
        $data = AdminCrudData::makeFromAdmin($admin, $hasher);

        return $this->crudEdit($data);
    }

    #[Route(path: '/admins/{id}/delete', name: 'app_admin_admin_delete', requirements: ['id' => '\d+'])]
    public function delete(Admin $admin): RedirectResponse|JsonResponse
    {
        $data = new AdminCrudData();
        $data->entity = $admin;

        return $this->crudDelete($data);
    }

    #[Route(path: '/admins/bulk/delete', name: 'app_admin_admin_bulk_delete')]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet compte ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' comptes ?';
    }
}
