<?php

namespace App\Manager;

use App\Repository\AdvertCategoryRepository;

class AdvertCategoryManager
{
    public function __construct(private readonly AdvertCategoryRepository $repository)
    {
    }

    public function getCategories()
    {
        return $this->repository->findBy(['enabled' => true], ['position' => 'ASC']);
    }
}
