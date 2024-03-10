<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BlogController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(
        private readonly PostRepository     $postRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaginatorInterface $paginator,
        private readonly Breadcrumbs        $breadcrumbs,
        SettingsManager                     $manager
    )
    {
        $this->settings = $manager->get();
    }

    #[Route(path: '/actualities', name: 'app_blog_index')]
    public function index(Request $request): Response
    {
        $this->isEnabled();

        $this->breadcrumb($this->breadcrumbs)->addItem('Actualités');

        $title = 'Actualités';
        $query = $this->postRepository->queryAll();

        return $this->renderListing($title, $query, $request);
    }

    #[Route(path: '/actualities/category/{slug}', name: 'app_blog_category')]
    public function category(Request $request, $slug): Response|NotFoundHttpException
    {
        $this->isEnabled();

        $category = $this->categoryRepository->getEnabledBySlug($slug);

        if (!$category) {
            return $this->createNotFoundException('Bad request');
        }

        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Actualités', $this->generateUrl('app_blog_index'))
            ->addItem($category->getName());

        $title = $category->getName();
        $query = $this->postRepository->queryAll($category);

        return $this->renderListing($title, $query, $request, ['category' => $category]);
    }

    #[Route(path: '/actualities/{slug}', name: 'app_blog_show')]
    public function show(Post $post): Response
    {
        $this->isEnabled();

        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Actualités', $this->generateUrl('app_blog_index'))
            ->addItem($post->getTitle());

        return $this->render('site/blog/show.html.twig', ['post' => $post]);
    }

    public function partial(): Response
    {
        return $this->render('site/blog/_partial.html.twig', ['posts' => $this->postRepository->findRecent(3)]);
    }

    public function categories(): Response
    {
        return $this->render('site/layout/_category.html.twig', ['categories' => $this->categoryRepository->getCategories()]);
    }

    public function last(): Response
    {
        return $this->render('site/layout/_last_post.html.twig', ['lastPosts' => $this->postRepository->findRecent(5)]);
    }

    private function renderListing(string $title, Query $query, Request $request, array $params = []): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $this->paginator->paginate($query, $page, 5);

        if ($page > 1) {
            $title .= ", page $page";
        }

        $categories = $this->categoryRepository->findWithCount();

        return $this->render('site/blog/index.html.twig', array_merge([
            'posts' => $posts,
            'categories' => $categories,
            'page' => $page,
            'title' => $title
        ], $params));
    }

    private function isEnabled(): void
    {
        if (!$this->settings->isActiveBlog()) {
            throw new NotFoundHttpException('Bad request');
        }
    }
}

