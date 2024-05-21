<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('activation', ChoiceType::class,[
            'choices'  => [
                'ativado' => true,
                'desativado' => false,
            ],
            'label'    => 'Ativação :',
            'data'     => false, // Default value if null
            
        ]) 
            ->add('resp',null,  array(
                'label' => 'Nome Responsável :'
            ))
            ->add('resp_email', null,  array(
                'label' => 'Email Responsável :'
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
