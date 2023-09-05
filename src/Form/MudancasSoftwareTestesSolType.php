<?php

namespace App\Form;

use App\Entity\MudancasSoftware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MudancasSoftwareTestesSolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('docTestSol', FileType::class,[
                'data_class' => null,
                'multiple' => true,
                'constraints' => [
                    new File([
                    'maxSize' => '100m',
                    'mimeTypes' => ['application/pdf','application/msword','application/vnd.ms-excel',
                                    'application/vnd.oasis.opendocument.text'
                                    ,'application/vnd.oasis.opendocument.text-flat-xml'
                                    ,'application/vnd.oasis.opendocument.text-template'
                                    ,'application/vnd.oasis.opendocument.text-web'
                                    ,'application/vnd.oasis.opendocument.text-master'
                                    ,'application/vnd.oasis.opendocument.graphics'
                                    ,'application/vnd.oasis.opendocument.graphics-flat-xml'
                                    ,'application/vnd.oasis.opendocument.graphics-template'
                                    ,'application/vnd.oasis.opendocument.presentation'
                                    ,'application/vnd.oasis.opendocument.presentation-flat-xml'
                                    ,'application/vnd.oasis.opendocument.presentation-template'
                                    ,'application/vnd.oasis.opendocument.spreadsheet'
                                    ,'application/vnd.oasis.opendocument.spreadsheet-flat-xml'
                                    ,'application/vnd.oasis.opendocument.spreadsheet-template'
                                    ,'application/vnd.oasis.opendocument.chart'
                                    ,'application/vnd.oasis.opendocument.formula'
                                    ,'application/vnd.oasis.opendocument.image'
                                    ,'application/vnd.sun.xml.writer'
                                    ,'application/vnd.sun.xml.writer.template'
                                    ,'application/vnd.sun.xml.writer.global'
                                    ,'application/vnd.stardivision.writer'
                                    ,'application/vnd.stardivision.writer-global'
                                    ,'application/vnd.sun.xml.calc'
                                    ,'application/vnd.sun.xml.calc.template'
                                    ,'application/vnd.stardivision.calc'
                                    ,'application/vnd.stardivision.chart'
                                    ,'application/vnd.sun.xml.impress'
                                    ,'application/vnd.sun.xml.impress.template'
                                    ,'application/vnd.stardivision.impress'
                                    ,'application/vnd.sun.xml.draw'
                                    ,'application/vnd.sun.xml.draw.template'
                                    ,'application/vnd.stardivision.draw'
                                    ,'application/vnd.sun.xml.math'
                                    ,'application/vnd.stardivision.math'
                                    ,'application/vnd.sun.xml.base'
                                    ,'application/vnd.openofficeorg.extension'
                                    ,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                    ,'application/vnd.ms-word.document.macroenabled.12'
                                    ,'application/vnd.openxmlformats-officedocument.wordprocessingml.template'
                                    ,'application/vnd.ms-word.template.macroenabled.12'
                                    ,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                    ,'application/vnd.ms-excel.sheet.macroenabled.12'
                                    ,'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
                                    ,'application/vnd.ms-excel.template.macroenabled.12'
                                    ,'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                                    ,'application/vnd.ms-powerpoint.presentation.macroenabled.12'
                                    ,'application/vnd.openxmlformats-officedocument.presentationml.template'
                                    ,'application/vnd.ms-powerpoint.template.macroenabled.12'
                    ],
                    'mimeTypesMessage' => 'Carregue um arquivo pdf, word ou excel válido',
                    ])],
                'required' => false,]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MudancasSoftware::class,
        ]);
    }
}
