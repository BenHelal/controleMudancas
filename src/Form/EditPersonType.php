<?php

namespace App\Form;

use App\Entity\Departemant;
use App\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'departemant',
                EntityType::class,
                array(
                    'class' => Departemant::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')->orderBy('s.id', 'DESC');
                    },
                    'choice_label' => 'name',
                    'choice_value'=> 'name',
                    'label' => 'Área Responsável pela mudança'
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
