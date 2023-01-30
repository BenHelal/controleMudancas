<?php

namespace App\Form;

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
                'label'=> 'Nome da mudança no nansen',
                'empty_data' => ''
            ])
            ->add('nansenNumber',null,[
                'label'=> 'Codigo da nansen',
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
            ->add('startMudancas', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'label' => 'Data estimada de Início',
                'required'   => false,
            ))
            ->add('endMudancas', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'label' => 'Data estimada de Termino',
                'required'   => false,
            ))
            ->add('effictiveStartDate', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'label' => 'Data Efetiva de Início',
                'required'   => false,
            ))
            ->add('cost',TextareaType::class, [
                'label' => 'Custo',
                'required'   => false,
            ])
            ->add('impDesc',TextareaType::class, [
                'label' => 'Evidencia de Implementação',
                'required'   => false,
            ])
            ->add('photo', FileType::class, array(
                'data_class' => null,
                'label' => 'foto (jpeg)',
                'constraints' => [
                    new File([
                    'maxSize' => '100m',
                    'mimeTypes' => [
                        'image/jpeg',
                    ],
                    'mimeTypesMessage' => 'Carregue uma imagem jpeg válida',
                    ])],
                // make it optional so you don't have to re-upload the png file
                // every time you edit the Product details
                'required' => false,
                )
            )
            
            ->add('pdf', FileType::class, array(
                'data_class' => null,
                'label' => 'Arquivo (pdf, word, excel)',
                'constraints' => [
                    new File([
                    'maxSize' => '100m',
                    'mimeTypes' => ['application/pdf','application/msword','application/vnd.ms-excel'
                    ],
                    'mimeTypesMessage' => 'Carregue um arquivo pdf, word ou excel válido',
                    ])],
                // make it optional so you don't have to re-upload the png file
                // every time you edit the Product details
                'required' => false,
                )
            )
            ->add('appGest', ChoiceType::class, [
                'choices'  => [
                    'Sim' => 1,
                    'Nao' => 2,
                ],
                'label'    => 'Aprovado ?',
                'required' => false,
            ])
            ->add('comGest', null, [
                'label'    => 'Comente :',
                'required' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mudancas::class,
        ]);
    }
}
