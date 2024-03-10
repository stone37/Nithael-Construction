<?php

namespace App\Twig;

use App\Util\BreadcrumbUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    public function __construct(private readonly BreadcrumbUtil $util)
    {
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('breadcrumb', [$this->util, 'addBreadcrumb'])
        );
    }
}