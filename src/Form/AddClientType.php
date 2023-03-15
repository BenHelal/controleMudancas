<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name',null,  array(
            'label' => 'Nome Cliente :'
        ))
        
        ->add('cnpj',null,  array(
            'label' => 'CNPJ :'
        ))
        
        ->add('name',null,  array(
            'label' => 'Nome Cliente :'
        ))
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
