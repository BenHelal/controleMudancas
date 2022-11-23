<?php

namespace App\Form;

use App\Entity\Requestper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('justification',TextareaType::class,[
                'label' => 'Justificativa','attr' => array(
                    'placeholder' => 'escreva a justificativa para exigir a permissão'
                )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Requestper::class,
        ]);
    }
}
