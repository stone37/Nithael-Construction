<?php

namespace App\Form;

use App\Data\SettingsModuleCrudData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activeReference', SwitchType::class, ['label' => 'Activé le module de reference'])
            ->add('activeBlog', SwitchType::class, ['label' => 'Activé le module de blog']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SettingsModuleCrudData::class
        ]);
    }
}
