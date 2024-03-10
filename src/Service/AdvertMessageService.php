<?php

namespace App\Service;

use App\Data\AdvertMessageData;
use App\Entity\Advert;
use App\Entity\MessageRequest;
use App\Exception\TooManyAdvertMessageException;
use App\Mailing\Mailer;
use App\Repository\MessageRequestRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdvertMessageService
{
    public function __construct(
        private readonly MessageRequestRepository $repository,
        private readonly EntityManagerInterface   $em,
        private readonly Mailer          $mailer
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws TooManyAdvertMessageException
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function send(AdvertMessageData $data, Advert $advert, Request $request): void
    {
        $messageRequest = (new MessageRequest())->setRawIp($request->getClientIp());
        $lastRequest = $this->repository->findLastRequestForIp($messageRequest->getIp());

        if ($lastRequest && $lastRequest->getCreatedAt() > new DateTime('- 1 hour')) {
            throw new TooManyAdvertMessageException();
        }

        if (null !== $lastRequest) {
            $lastRequest->setCreatedAt(new DateTime());
        } else {
            $this->em->persist($messageRequest);
        }

        $this->em->flush();

        $sender = $this->mailer->createEmail('mails/message/send.twig', ['data' => $data, 'advert' => $advert])
            ->to('contact@nithaelconstruction.com')
            ->subject("Nithael Construction::Contact : {$data->firstname} {$data->lastname} ({$data->phone})");

        $this->mailer->send($sender);
    }
}

