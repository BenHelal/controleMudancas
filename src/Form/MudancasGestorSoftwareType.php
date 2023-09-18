<?php

namespace App\Form;

use App\Entity\MudancasSoftware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\File;

class MudancasGestorSoftwareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priorityGestor',null,[
                'label'    => 'Posição na fila de prioridades ',
            ])
            ->add('iniciar', ChoiceType::class, [
                'choices'  => [
                    'Sim' => 1,
                    'Nao' => 2,
                ],
                'label'    => 'Iniciar',
            ])
            ->add('dateInicio',null,[
                'label'    => 'Data de início ',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MudancasSoftware::class,
        ]);
    }
}
