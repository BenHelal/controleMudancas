<?php

namespace App\Form;

use App\Entity\EmailToSendConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailToSendConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titleOfMessage',null,[
                'label' => 'Referência ao código'
            ])
            ->add('subjectMessage', null, [
                'label' => 'Assunto do e-mail'
            ])
            ->add('body',TextareaType::class, [
                'label' => 'Corpo do e-mail',
                'attr' => ['class' => 'form-control', 'rows' => 8],
            ])
            ->add('sendTo', ChoiceType::class, [
                'label' => 'Enviar para',
                'choices' => [
                    'Gestor' => 'Gestor',
                    'Gerente de aprovação' => 'Gerente de aprovação',
                    'Gerente Solicitante' => 'Gerente Solicitante',
                    'Desenvolvedor' => 'Desenvolvedor',
                    'Solicitante' => 'Solicitante',
                    'Coordenador da Área Impactada' => 'Coordenador da Área Impactada'
                ],])
            ->add('sendFrom', ChoiceType::class, [
                'label' => 'Enviar de',
                'choices' => [
                    'Gestor' => 'Gestor',
                    'Gerente de aprovação' => 'Gerente de aprovação',
                    'Gerente Solicitante' => 'Gerente Solicitante',
                    'Desenvolvedor' => 'Desenvolvedor',
                    'Solicitante' => 'Solicitante',
                    'Coordenador da Área Impactada' => 'Coordenador da Área Impactada'
                ],
                // Optional: Set the preferred choices or additional options
                // 'preferred_choices' => ['value1'],
                // 'attr' => ['class' => 'custom-class'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\EmailToSendConfig',
        ]);
    }
}
