<?php

namespace App\Controller\Admin;

use App\Data\ReferenceCrudData;
use App\Entity\Reference;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ReferenceController extends CrudController
{
    protected string $entity = Reference::class; 
    protected string $templatePath = 'reference';
    protected string $routePrefix = 'app_admin_reference';
    protected string $createFlashMessage = 'Une reference a été crée';
    protected string $editFlashMessage = 'Une reference a été mise à jour';
    protected string $deleteFlashMessage = 'La reference a été supprimée';
    protected string $deleteMultiFlashMessage = 'Les references ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les references n\'a pas pu être supprimé !';

    #[Route(path: '/references', name: 'app_admin_reference_index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderby('row.position', 'ASC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/references/create', name: 'app_admin_reference_create')]
    public function create(): Response
    {
        $entity = new Reference();
        $data = new ReferenceCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/references/{id}/edit', name: 'app_admin_reference_edit', requirements: ['id' => '\d+'])]
    public function edit(Reference $reference): Response
    {
        $data = new ReferenceCrudData($reference);

        return $this->crudEdit($data);
    }

    #[Route(path: '/references/{id}/move', name: 'app_admin_reference_move', requirements: ['id' => '\d+'])]
    public function move(Reference $reference): RedirectResponse
    {
        return $this->crudMove($reference);
    }

    #[Route(path: '/references/{id}/delete', name: 'app_admin_reference_delete', requirements: ['id' => '\d+'])]
    public function delete(Reference $reference): RedirectResponse|JsonResponse
    {
        $data = new ReferenceCrudData($reference);
        
        return $this->crudDelete($data);
    }

    #[Route(path: '/references/bulk/delete', name: 'app_admin_reference_bulk_delete')]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cette reference ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' references ?';
    }
}
