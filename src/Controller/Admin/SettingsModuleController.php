<?php

namespace App\Controller\Admin;

use App\Data\SettingsModuleCrudData;
use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class SettingsModuleController extends CrudController
{
    protected string $entity = Settings::class;
    protected string $templatePath = 'settingsModule';
    protected string $routePrefix = 'app_admin_settings_module';
    protected string $hybridIndexFlashMessage = 'Les paramètres des modules ont été mise à jour';

    #[Route(path: '/settings/modules', name: 'app_admin_settings_module_index')]
    public function module(SettingsRepository $repository): RedirectResponse|Response
    {
        $settings = $repository->getSettings();

        if (!$settings) {
            return $this->redirectToRoute('app_admin_settings_index');
        }

        $data = new SettingsModuleCrudData($settings);

        return $this->crudHybridIndex($data);
    }
}
