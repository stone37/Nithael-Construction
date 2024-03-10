<?php

namespace App\Subscriber;

use App\Entity\AdvertPicture;
use App\Event\AdvertPreCreateEvent;
use App\Event\AdvertPreEditEvent;
use App\Repository\AdvertPictureRepository;
use App\Service\UploadService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

class AdvertUploadSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UploadService  $upload,
        private readonly AdvertPictureRepository $advertPictureRepository
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AdvertPreCreateEvent::class => 'onUpload',
            AdvertPreEditEvent::class => 'onEditUpload',
        ];
    }

    public function onEditUpload(AdvertPreEditEvent $event): void
    {
        $advert  = $event->getAdvert();
        $request = $event->getRequest();

        $images = $this->upload->getFilesUpload($request->getSession());

        foreach ($images as $image) {
            $picture = (new AdvertPicture())
                ->setFile(new File($image->getPathname()));

            $this->advertPictureRepository->add($picture);

            $advert->addPicture($picture);
        }
    }

    public function onUpload(AdvertPreCreateEvent $event): void
    {
        $advert  = $event->getAdvert();
        $request = $event->getRequest();

        $images = $this->upload->getFilesUpload($request->getSession());

        foreach ($images as $image) {
            $picture = (new AdvertPicture())
                ->setFile(new File($image->getPathname()));

            $advert->addPicture($picture);
        }
    }
}
