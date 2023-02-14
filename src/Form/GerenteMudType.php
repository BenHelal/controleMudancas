<?php

namespace App\Form;

use App\Entity\Mudancas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GerenteMudType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('managerUserApp', ChoiceType::class, [
            'choices'  => [
                'Sim' => 1,
                'Nao' => 2,
            ],
            'label'    => 'Deseja aprovar?',
            'required' => false,
        ])
        ->add('managerUserComment', null, [
            'label'    => 'Comentário :',
            'required' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mudancas::class,
        ]);
    }
}
