<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Sector;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,  array(
                'label' => 'Nome :'
            ))
            ->add('email', null,  array(
                'label' => 'Email :'
            ))
            ->add(
                'function',
                EntityType::class,
                array(
                    'class' => Sector::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')->orderBy('s.id', 'DESC');
                    },
                    'choice_label' => 'name',
                    'label' => 'Setor relacionado :'
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
