<?php

namespace App\Form;

use App\Entity\SectorProcess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectorProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', null, [
                'label'    => 'Comente :',
                'required' => false,
            ])
            ->add('appSectorMan', ChoiceType::class, [
                'choices'  => [
                    'Sim' => true,
                    'Nao' => false,
                ],
                'label'    => 'Aprovado ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SectorProcess::class,
        ]);
    }
}
