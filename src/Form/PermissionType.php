<?php

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**ler
         * 
         * administrador
ler criar
ler criar atualização
tudo */
        $builder
        ->add('permission', ChoiceType::class,[
            'choices'  => [
                'ler' => 'ler',
                'ler criar' => 'ler criar',
                'ler criar atualização' => 'ler criar atualização',
                'tudo' => 'tudo',
            ],
            'label'    => 'Permissão :',
            
        ]) 
        ->add('role', ChoiceType::class,[
                'choices'  => [
                    'administrador' => 'admin',
                    'sem Permissão' => '',
                ],
                'label'    => 'administrador :',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
