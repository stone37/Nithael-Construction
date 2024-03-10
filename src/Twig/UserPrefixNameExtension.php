<?php

namespace App\Twig;

use App\Util\UserPrefixNameUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserPrefixNameExtension extends AbstractExtension
{
    public function __construct(private readonly UserPrefixNameUtil $prefix)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('userPrefixName', [$this->prefix, 'prefix']),
            new TwigFunction('dataPrefixName', [$this->prefix, 'dataPrefix']),
        ];
    }
}
