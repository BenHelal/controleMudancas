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

class MudancasSoftgestorImpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('implemented', ChoiceType::class, [
                'choices'  => [
                    'Sim' => 1,
                    'Nao' => 2,
                ],
                'label'    => 'Deseja aprovar?',
                'required' => true,
            ])
            ->add('impDesc', TextareaType::class, [
                'label' => 'Evidencia de Implementação'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mudancas::class,
        ]);
    }
}
