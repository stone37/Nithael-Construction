<?php

namespace App\Controller\Admin;

use App\Data\PostCrudData;
use App\Entity\Post;
use App\Event\PostCreatedEvent;
use App\Event\PostDeletedEvent;
use App\Event\PostUpdatedEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class BlogController extends CrudController
{
    protected string $entity = Post::class;
    protected string $templatePath = 'blog';
    protected string $routePrefix = 'app_admin_blog';
    protected string $createFlashMessage = 'Un article a été crée';
    protected string $editFlashMessage = 'Un article a été mise à jour';
    protected string $deleteFlashMessage = 'Un article a été supprimé';
    protected string $deleteMultiFlashMessage = 'Les articles ont été supprimés';
    protected string $deleteErrorFlashMessage = 'Désolé, les articles n\'a pas pu être supprimé !';
    protected array $events = [
        'update' => PostUpdatedEvent::class,
        'delete' => PostDeletedEvent::class,
        'create' => PostCreatedEvent::class
    ];

    #[Route(path: '/blog', name: 'app_admin_blog_index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/blog/create', name: 'app_admin_blog_create')]
    public function create(): Response
    {
        $data = new PostCrudData(); 
        $data->author = $this->getUser();
        $data->entity = new Post();

        return $this->crudNew($data);
    }

    #[Route(path: '/blog/{id}/edit', name: 'app_admin_blog_edit', requirements: ['id' => '\d+'])]
    public function edit(Post $post): Response
    {
        $data = PostCrudData::makeFromPost($post);

        return $this->crudEdit($data);
    }

    #[Route(path: '/blog/{id}/delete', name: 'app_admin_blog_delete', requirements: ['id' => '\d+'])]
    public function delete(Post $post): RedirectResponse|JsonResponse
    {
        $data = new PostCrudData();
        $data->entity = $post;

        return $this->crudDelete($data);
    }

    #[Route(path: '/blog/bulk/delete', name: 'app_admin_blog_bulk_delete')]
    public function deleteBulk(): RedirectResponse|JsonResponse
    {
        return $this->crudMultiDelete();
    }

    public function getDeleteMessage(): string
    {
        return 'Être vous sur de vouloir supprimer cet article ?';
    }

    public function getDeleteMultiMessage(int $number): string
    {
        return 'Être vous sur de vouloir supprimer ces ' . $number . ' articles ?';
    }
}
