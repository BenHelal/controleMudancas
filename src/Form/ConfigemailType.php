<?php

namespace App\Form;

use App\Entity\ConfigEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigemailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('host')
            ->add('smtpAuth')
            ->add('port')
            ->add('username')
            ->add('password')
            ->add('emailSystem')
            ->add('titleObj')
            ->add('subject')
            ->add('chartSet')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConfigEmail::class,
        ]);
    }
}
