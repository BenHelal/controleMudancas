<?php

namespace App\Form\GestorSoftware;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class iniciarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('iniciar', ChoiceType::class, [
                'choices'  => [
                    'Sim' => 1,
                    'Nao' => 2,
                ],
                'label'    => 'Iniciar',
            ])
            ->add('dateInicio', null, [
                'label'    => 'Data de início',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
