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
            ->add('activeReview', SwitchType::class, ['label' => 'Activé le module de témoignage'])
            ->add('activeBlog', SwitchType::class, ['label' => 'Activé le module de blog'])
            ->add('activeAchieve', SwitchType::class, ['label' => 'Activé le module de réalisation'])
            ->add('activeTeam', SwitchType::class, ['label' => 'Activé le module de membre de l\'équipe']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SettingsModuleCrudData::class
        ]);
    }
}
