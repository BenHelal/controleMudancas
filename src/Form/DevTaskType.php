<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Steps;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startdevdate',null,  array(
                'label' => 'data de início do desenvolvimento :'
            ))
            ->add('enddevdatet', null,  array(
                'label' => 'data final do desenvolvimento :'
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Steps::class,
        ]);
    }
}
