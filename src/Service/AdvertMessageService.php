<?php

namespace App\Service;

use App\Entity\ContactRequest;
use App\Exception\TooManyContactException;
use App\Data\ContactData;
use App\Repository\ContactRequestRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ContactService
{
    public function __construct(
        private readonly ContactRequestRepository $repository,
        private readonly EntityManagerInterface   $em,
        private readonly MailerInterface $mailer
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws TooManyContactException
     */
    public function send(ContactData $data, Request $request): void
    {
        $contactRequest = (new ContactRequest())->setRawIp($request->getClientIp());
        $lastRequest = $this->repository->findLastRequestForIp($contactRequest->getIp());

        if ($lastRequest && $lastRequest->getCreatedAt() > new DateTime('- 1 hour')) {
            throw new TooManyContactException();
        }

        if (null !== $lastRequest) {
            $lastRequest->setCreatedAt(new DateTime());
        } else {
            $this->em->persist($contactRequest);
        }

        $this->em->flush();

        $message = (new Email())
            ->text($data->content)
            ->subject("Nithael Construction::Contact : {$data->name} ({$data->phone})")
            ->from('noreply@nithaelconstruction.com')
            ->replyTo(new Address($data->email, $data->name))
            ->to('contact@nithaelconstruction.com');

        $this->mailer->send($message);
    }
}

