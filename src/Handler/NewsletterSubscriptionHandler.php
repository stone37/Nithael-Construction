<?php

namespace App\Handler;

use App\Entity\NewsletterData;
use App\Repository\NewsletterDataRepository;

class NewsletterSubscriptionHandler
{
    public function __construct(private readonly NewsletterDataRepository $repository)
    {
    }

    public function subscribe(string $email): bool
    {
        $newsletterData = $this->repository->findOneBy(['email' => $email]);

        if ($newsletterData instanceof NewsletterData) {
            return false;
        }

        $this->createNewsletter($email);

        return true;
    }

    public function unsubscribe(NewsletterData $data): void
    {
        $this->deleteNewsletter($data);
    }

    private function createNewsletter($email): void
    {
        $newsletter = (new NewsletterData())->setEmail($email);

        $this->repository->add($newsletter, true);
    }

    private function deleteNewsletter(NewsletterData $data): void
    {
        $this->repository->remove($data, true);
    }
}

