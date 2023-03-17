<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Departemant;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Sector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MudancasgestorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    
        $builder
            ->add('nomeMudanca',null,[
                'label'=> 'Nome da mudança'
            ])
            ->add('nansenName',null,[
                'label'=> 'Nome do Projeto Nansen',
                'empty_data' => ''
            ])
            ->add('nansenNumber',null,[
                'label'=> 'Código Nansen',
                'empty_data' => ''
            ])
            ->add('descMudanca',TextareaType::class,[
                'label' => 'Descrição da Mudança'
            ])
            ->add('descImpacto',TextareaType::class, [
                'label' => 'Descrição do Impacto'
            ])
            ->add('descImpactoArea',TextareaType::class, [
                'label' => 'Descrição do Impacto na área do Solicitante'
            ])
            ->add('justif',TextareaType::class, [
                'label' => 'Justificativa'
            ])
            ->add('areaImpact',EntityType::class,array(
                'class' => Sector::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')->orderBy('s.id','DESC');
                },
                'choice_label' => 'name',
                'label'=> 'Área impactada',
                
                'placeholder' => 'Área impactada',
                'expanded'  => false,
                'multiple' => true))
            ->add(
                'areaResp',
                EntityType::class, 
                array(
                    'class' => Sector::class,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('s')->orderBy('s.id','DESC');
                    },
                    'choice_label' => 'name',
                    'label' => 'Área Responsável pela mudança'
                
            ))
            ->add(
                'mangerMudancas',
                EntityType::class, 
                array(
                    'class' => Person::class,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('s')
                        ->orderBy('s.name','ASC');
                    },
                    'choice_label' => 'name',
                    'label' => 'Gestor da mudança',
                    'required'   => false,
                
            ))
            ->add('startMudancas', null, array(
                'label' => 'Data estimada de Início',
                'required'   => false,
            ))
            ->add('endMudancas', null, array(
                'label' => 'Data estimada de Termino',
                'required'   => false,
            ))
            ->add('effictiveStartDate',null, array(
                'label' => 'Data Efetiva de Início',
                'required'   => false,
            ))
            ->add('cost',TextareaType::class, [
                'label' => 'Custo',
                'required'   => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mudancas::class,
        ]);
    }
}
