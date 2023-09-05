<?php

namespace App\Form;

use App\Entity\MudancasSoftware;
use App\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MudancasSoftwareGerenteDeAprovacaoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priority',null, [
                'label' => 'Posição na fila de prioridades'
            ])
            ->add('date',null, [
                'label' => 'Data da aprovação'
            ])
            ->add('gestor',
            EntityType::class, 
            array(
                'class' => Person::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                    ->orderBy('s.name','ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Selecione uma Opção',
                'label' => 'Gestor da mudança'
            
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MudancasSoftware::class,
        ]);
    }
}
