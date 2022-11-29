<?php

namespace App\Form;

use App\Entity\Departemant;
use App\Entity\Person;
use App\Entity\Sector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('Departemant',
            EntityType::class,
            array(
                'class' => Departemant::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'Departamento'
            ))
            ->add('coordinator',
            EntityType::class,
            array(
                'class' => Person::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'Coordenador'
            ))
            ->add('manager',
            EntityType::class,
            array(
                'class' => Person::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'DESC');
                },
                'choice_label' => 'name',
                'label' => 'Gerente'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sector::class,
        ]);
    }
}
