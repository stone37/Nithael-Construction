<?php

namespace App\Subscriber;

use App\Event\AdvertBadEvent;
use App\Event\AdvertInitEvent;
use App\Event\AdvertViewEvent;
use App\Exception\CategoryNotFoundException;
use App\Manager\OrphanageManager;
use App\Repository\AdvertCategoryRepository;
use App\Repository\AdvertRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvertSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AdvertCategoryRepository $repository,
        private readonly AdvertRepository         $advertRepository,
        private readonly OrphanageManager $orphanageManager
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AdvertInitEvent::class => 'onInit',
            AdvertBadEvent::class => 'onError',
            AdvertViewEvent::class => 'onView'
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function onInit(AdvertInitEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->isMethod('GET')) {
            $category = $this->repository->getEnabledBySlug($request->attributes->get('category_slug'));

            if (null === $category) {
                throw new CategoryNotFoundException();
            }

            $request->getSession()->set($this->provideKey(), []);
            $this->orphanageManager->initClear($request->getSession());
        }
    }

    public function onError(AdvertBadEvent $event): void
    {
        $request = $event->getRequest();

        if ($event->getRequest()->isMethod('POST')) {
            $this->orphanageManager->initClear($request->getSession());
            $request->getSession()->set($this->provideKey(), []);
        }
    }

    public function onView(AdvertViewEvent $event): void
    {
        $advert = $event->getAdvert();

        $advert->setNumberOfViews($advert->getNumberOfViews() + 1);

        $this->advertRepository->flush();
    }

    private function provideKey(): string
    {
        return '_app_advert_images';
    }
}
