<?php

namespace App\Mailing;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{
    public function __construct(
        private readonly Environment     $twig,
        private readonly MailerInterface $mailer,
        private readonly ?string         $dkimKey = null
    )
    {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function createEmail(string $template, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from('noreply@nithaelconstruction.com')
            ->html($html)
            ->text($text);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email): void
    {
        if ($this->dkimKey) {
            $dkimSigner = new DkimSigner("file://{$this->dkimKey}", 'hostel.com', 'default');
            // On signe un message en attendant le fix https://github.com/symfony/symfony/issues/40131
            $message = new Message($email->getPreparedHeaders(), $email->getBody());
            $email = $dkimSigner->sign($message, []);
        }

        $this->mailer->send($email);
    }
}
