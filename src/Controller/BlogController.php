<?php

namespace App\Controller;

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
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository,
        private PaginatorInterface $paginator,
        private Breadcrumbs $breadcrumbs,
        SettingsManager $manager
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
    public function category(Category $category, Request $request): Response
    {
        $this->isEnabled();

        $this->breadcrumb($this->breadcrumbs)->addItem('Actualités');

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

    private function renderListing(string $title, Query $query, Request $request, array $params = []): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $this->paginator->paginate($query, $page, 10);

        if ($page > 1) {
            $title .= ", page $page";
        }

        if (0 === $posts->count()) {
            throw new NotFoundHttpException('Aucun articles ne correspond à cette page');
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

