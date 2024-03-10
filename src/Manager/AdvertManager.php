<?php

namespace App\Manager;

use App\Entity\Advert;
use App\Entity\AdvertCategory;
use App\Repository\AdvertCategoryRepository;
use App\Util\AdvertUtil;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;


class AdvertManager
{
    public function __construct(
        private readonly RequestStack             $request,
        private readonly AdvertCategoryRepository $categoryRepository,
        private readonly AdvertUtil $util
    )
    {
    }

    public function createAdvert(): Advert
    {
        return (new Advert())
            ->setCategory($this->getCategory());
    }

    public function createForm(): string
    {
        return $this->util->createForm($this->getCategorySlug());
    }

    public function editForm(Advert $advert): string
    {
        return $this->util->createForm($advert->getCategory()->getSlug());
    }

    public function viewRoute(): string
    {
        return $this->util->viewRoute($this->getCategorySlug());
    }

    public function viewEditRoute(Advert $advert): string
    {
        return $this->util->viewRoute($advert->getCategory()->getSlug());
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getCategory(): ?AdvertCategory
    {
        return $this->categoryRepository->getEnabledBySlug($this->getCategorySlug());
    }

    private function getCategorySlug(): string
    {
        return (string) $this->request->getCurrentRequest()->attributes->get('category_slug');
    }
}
