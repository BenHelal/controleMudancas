<?php

namespace App\Form;

use App\Entity\Requestper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestadminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('approves', ChoiceType::class,[
                'choices'  => [
                    'dar consentimento' => 'yes',
                    'Não dê consentimento' => 'no',
                ],
                'label'    => 'Dar consentimento :',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Requestper::class,
        ]);
    }
}
