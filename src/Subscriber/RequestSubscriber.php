<?php

namespace App\Subscriber;

use App\Manager\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly SettingsManager $manager
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onRequest'];
    }

    public function onRequest(RequestEvent $event): void
    {
        $route = $event->getRequest()->get('_route');

        if (str_contains($route, 'admin') && !str_contains($route, 'settings') && $event->isMainRequest()) {
            $settings = $this->manager->get();

            if (!$settings) {
                $response = new RedirectResponse($this->router->generate('app_admin_settings_index'));
                $event->setResponse($response);
            }
        }
    }
}