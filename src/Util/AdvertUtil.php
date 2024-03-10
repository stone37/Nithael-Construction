<?php

namespace App\Util;

class AdvertUtil
{
    public function createForm(string $category_slug): string
    {
        $prefix = $this->prefix($category_slug);

        return 'App\Form\Immobilier\\' . $prefix . 'Type';
    }

    public function viewRoute(string $category_slug): string
    {
        $prefix = $this->prefix($category_slug);

        return 'admin/advert/form/' . strtolower($prefix) .'.html.twig';
    }

    private function prefix(string $category_slug): string
    {
        $data = '';

        foreach (explode('-', $category_slug) as $item) {
            $data .= ucfirst($item);
        }

        return $data;
    }
}
