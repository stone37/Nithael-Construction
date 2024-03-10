<?php

namespace App\Controller\Admin;

use App\Controller\Traits\ControllerTrait;
use App\Controller\Traits\UploadTrait;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    use ControllerTrait;
    use UploadTrait;

    #[Route(path: '/upload/image', name: 'app_image_upload_add')]
    public function add(Request $request): NotFoundHttpException|JsonResponse
    { 
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        $files = $this->getFiles($request->files);

        foreach ($files as $file) {
            try {
                try {
                    $this->upload($file, $request->getSession());
                } catch (FileException) {}
            } catch (UploadException) {}
        }

        return new JsonResponse();
    }

    #[Route(path: '/upload/image/delete', name: 'app_image_upload_delete')]
    public function delete(Request $request): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        if (!$request->getSession()->has($this->provideKey())) {
            return $this->createNotFoundException('Bad request');
        }

        if (!$request->query->has('pos')) {
            return $this->createNotFoundException('Bad request');
        }

        $pos = $request->query->get('pos');

        $data = $request->getSession()->get($this->provideKey());

        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($this->getFindPath($request->getSession()))->name(''.key($data[$pos]).'');
        } catch (InvalidArgumentException) {
            $finder->append([]);
        }

        foreach ($finder as $file) {
            $system->remove((string) $file->getRealPath());
            array_splice($data, $pos, 1);
            $request->getSession()->set($this->provideKey(), $data);
        }

        return new JsonResponse();
    }
}

