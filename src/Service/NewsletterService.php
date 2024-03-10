<?php

namespace App\Service;

use App\Entity\Emailing;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NewsletterService
{
    public function __construct(
        private readonly Mailer          $mailer,
        private readonly SettingsManager $manager
    )
    {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendEmailing(Emailing $emailing): void
    {
        $sender = $this->mailer->createEmail('mails/newsletter/particular.twig', ['emailing' => $emailing])
            ->to($emailing->getDestinataire())
            ->subject($this->manager->get()->getName() . ' | '. $emailing->getSubject());

        $this->mailer->send($sender);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendNewsletterEmailing(Emailing $emailing, array $newsletters): void
    {
        foreach ($newsletters as $newsletter) {
            $sender = $this->mailer->createEmail('mails/newsletter/newsletter.twig', ['emailing' => $emailing])
                ->to($newsletter->getEmail())
                ->subject($this->manager->get()->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->send($sender);
        }
    }
}