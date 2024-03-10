<?php

namespace App\Controller;

use App\Controller\RequestDataHandler\AdvertListDataHandler;
use App\Controller\RequestDataHandler\PaginationDataHandler;
use App\Controller\Traits\ControllerTrait;
use App\Data\AdvertMessageData;
use App\Event\AdvertViewEvent;
use App\Exception\TooManyAdvertMessageException;
use App\Form\AdvertMessageType;
use App\Form\Filter\AdvertFilterType;
use App\Manager\AdvertManager;
use App\Model\AdvertSearch;
use App\Repository\AdvertRepository;
use App\Service\AdvertMessageService;
use Knp\Component\Pager\PaginatorInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class AdvertController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly AdvertRepository         $repository,
        private readonly AdvertManager            $manager,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly Breadcrumbs              $breadcrumbs,
        private readonly PaginatorInterface       $paginator,
        private readonly AdvertListDataHandler    $advertListDataHandler,
        private readonly PaginationDataHandler $paginationDataHandler,
        private readonly ReCaptcha $reCaptcha,
        private readonly AdvertMessageService $service,
    )
    {
    }

    #[Route(path: '/nos-offres', name: 'app_advert_index')]
    public function index(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Nos offres');

        $search = new AdvertSearch();
        $form = $this->createForm(AdvertFilterType::class, $search);
        $form->handleRequest($request);

        $requestData = array_merge($search->toArray(), $request->query->all());

        $data = array_merge(
            $this->advertListDataHandler->retrieveData($requestData),
            $this->paginationDataHandler->retrieveData($requestData)
        );

        $adverts = $this->paginator->paginate( 
            $this->repository->getAdvertLists($data),
            $request->query->getInt('page', $data[PaginationDataHandler::PAGE_INDEX]),
            $data[PaginationDataHandler::LIMIT_INDEX]);

        return $this->render('site/advert/index.html.twig', [
            'adverts' => $adverts,
            'form' => $form->createView(),
            'search' => $search
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: '/nos-offres/{slug}', name: 'app_advert_show')]
    public function show(Request $request, string $slug): Response
    {
        $advert = $this->repository->getEnabledBySlug($slug);

        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Nos offres', $this->generateUrl('app_advert_index'))
            ->addItem($advert->getTitle());

        $this->dispatcher->dispatch(new AdvertViewEvent($advert));

        $data = new AdvertMessageData();
        $form = $this->createForm(AdvertMessageType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()) {
                try {
                    $this->service->send($data, $advert, $request);
                } catch (TooManyAdvertMessageException) {
                    $this->addFlash('error', 'Vous avez fait trop de demandes de contact consécutives.');

                    return $this->redirectToRoute('app_advert_show', ['slug' => $advert->getSlug()]);
                }

                $this->addFlash('success', 'Votre demande de reservation a été transmis, nous vous répondrons dans les meilleurs délais.');

            } else {
                $this->addFlash('error', 'Erreur pendant l\'envoi de votre message');
            }

            return $this->redirectToRoute('app_advert_show', ['slug' => $advert->getSlug()]);
        }

        return $this->render('site/advert/show.html.twig', [
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    public function partial(): Response
    {
        return $this->render('site/advert/_partial.html.twig', ['adverts' => $this->repository->findRecent(3)]);
    }

    public function last(): Response
    {
        return $this->render('site/layout/_last_advert.html.twig', ['lastAdverts' => $this->repository->findRecent(5)]);
    }
}
