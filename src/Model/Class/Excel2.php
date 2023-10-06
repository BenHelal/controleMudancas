<?php

namespace App\Model\Class;

use App\Entity\ExcelConfig;
use App\Model\Interface\ExcelBuilderInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\XmlConfiguration\IniSetting;

class Excel2
{
    public function generateExcel($fournisor, $services, $termo, $excelConfig)
    {

        if ($services != null) {
            $spreadsheet  = new Spreadsheet();

            /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
            $sheet = $spreadsheet->getActiveSheet();


            /**
             * Design 
             */
            $spreadsheet
                ->getActiveSheet()
                ->getStyle('A8')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB(ltrim($excelConfig->getBlockTitleBack(), '#'));
            $spreadsheet
                ->getActiveSheet()
                ->getStyle('A15')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB(ltrim($excelConfig->getBlockTitleBack(), '#'));
            $spreadsheet
                ->getActiveSheet()
                ->getStyle('A26')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB(ltrim($excelConfig->getBlockTitleBack(), '#'));
            $spreadsheet
                ->getActiveSheet()
                ->getStyle('A30')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB(ltrim($excelConfig->getBlockTitleBack(), '#'));

            $TitleBar = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => ltrim($excelConfig->getTitleColor(), '#')),
                    'size'  => 15,
                    'name'  => $excelConfig->getTitleFont()->getName(),
                )
            );

            $TitleBlock = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => ltrim($excelConfig->getBlockTitleColor(), '#')),
                    'size'  => 10,
                    'name'  => $excelConfig->getBlockTitleFont()->getName(),
                )
            );

            $titleAttribute = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => ltrim($excelConfig->getBlockTitleColor(), '#')),
                    'size'  => 8,
                    'name'  => $excelConfig->getBlockTitleFont()->getName(),
                )
            );

            $termBlock = array(
                'font' => array(
                    'italic' => true,
                    'bold' => true,
                    'color' => array('rgb' => ltrim($excelConfig->getTextColor(), '#')),
                    'size'  => 9,
                    'name'  => $excelConfig->getTextFont()->getName(),
                )
            );

            $dataFournisor = array(
                'font' => array(
                    'italic' => false,
                    'bold' => false,
                    'color' =>  array('rgb' => ltrim($excelConfig->getDataColor(), '#')),
                    'size'  => 9,
                    'name'  => $excelConfig->getDataFont()->getName(),
                )
            );

            /**---------------------------------- */
            $sheet->getStyle('C1')->applyFromArray($TitleBar);
            $sheet->getStyle('M1')->applyFromArray($TitleBar);
            $sheet->getStyle('A8')->applyFromArray($TitleBlock);
            $sheet->getStyle('A15')->applyFromArray($TitleBlock);
            $sheet->getStyle('A26')->applyFromArray($TitleBlock);
            $sheet->getStyle('A30')->applyFromArray($TitleBlock);
            $sheet->getStyle('A16')->applyFromArray($termBlock);
            $sheet->getStyle('I31')->applyFromArray($termBlock);
            $sheet->getStyle('A31')->applyFromArray($titleAttribute);
            $sheet->getStyle('C31')->applyFromArray($titleAttribute);
            $sheet->getStyle('A35')->applyFromArray($titleAttribute);
            $sheet->getStyle('C35')->applyFromArray($titleAttribute);
            $sheet->getStyle('D35')->applyFromArray($titleAttribute);
            $sheet->getStyle('F35')->applyFromArray($titleAttribute);
            $sheet->getStyle('H35')->applyFromArray($titleAttribute);
            $sheet->getStyle('J35')->applyFromArray($titleAttribute);
            $sheet->getStyle('L35')->applyFromArray($titleAttribute);
            $sheet->getStyle('M35')->applyFromArray($titleAttribute);
            $sheet->getStyle('A31')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('C31')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('A35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('C35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('D35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('F35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('H35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('J35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('L35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('M35')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('D6')->applyFromArray($dataFournisor);
            $sheet->getStyle('E9')->applyFromArray($dataFournisor);
            $sheet->getStyle('C10')->applyFromArray($dataFournisor);
            $sheet->getStyle('L10')->applyFromArray($dataFournisor);
            $sheet->getStyle('C11')->applyFromArray($dataFournisor);
            $sheet->getStyle('K11')->applyFromArray($dataFournisor);
            $sheet->getStyle('C12')->applyFromArray($dataFournisor);
            $sheet->getStyle('C13')->applyFromArray($dataFournisor);
            $sheet->getStyle('A27')->applyFromArray($dataFournisor);
            $sheet->getStyle('C1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('M1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A8')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A15')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A26')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A30')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('G39')->getAlignment()->setHorizontal('right');

            /**---------------------------------- */


            /**
             * Structure File
             */
            $sheet->mergeCells("A1:B4");
            $sheet->mergeCells("C1:L4");
            $sheet->mergeCells("M1:N4");
            $sheet->mergeCells("B5:G5");
            $sheet->mergeCells("I5:N5");
            $sheet->mergeCells("A6:C6");
            $sheet->mergeCells("D6:G6");
            $sheet->mergeCells("H6:K6");
            $sheet->mergeCells("L6:N6");
            $sheet->mergeCells("A35:B35");
            $sheet->mergeCells("D35:E35");
            $sheet->mergeCells("F35:G35");
            $sheet->mergeCells("H35:I35");
            $sheet->mergeCells("J35:K35");
            $sheet->mergeCells("M35:N35");
            $sheet->mergeCells("A7:N7");
            $sheet->mergeCells("A8:N8");
            $sheet->mergeCells("A9:D9");
            $sheet->mergeCells("E9:N9");
            $sheet->mergeCells("A10:B10");
            $sheet->mergeCells("C10:I10");
            $sheet->mergeCells("J10:K10");
            $sheet->mergeCells("L10:N10");
            $sheet->mergeCells("A11:B11");
            $sheet->mergeCells("C11:I11");
            $sheet->mergeCells("K11:N11");
            $sheet->mergeCells("A12:B12");
            $sheet->mergeCells("C12:N12");
            $sheet->mergeCells("A13:B13");
            $sheet->mergeCells("C13:N13");
            $sheet->mergeCells("A14:N14");
            $sheet->mergeCells("A15:N15");
            $sheet->mergeCells("A16:N24");
            $sheet->mergeCells("A25:N25");
            $sheet->mergeCells("A26:N26");
            $sheet->mergeCells("A27:N28");
            $sheet->mergeCells("A29:N29");
            $sheet->mergeCells("A30:N30");
            $sheet->mergeCells("I31:N34");
            $sheet->mergeCells("C34:H34");
            $sheet->mergeCells("A31:B31");
            $sheet->mergeCells("C31:H31");
            $sheet->mergeCells("A32:B32");
            $sheet->mergeCells("C32:H32");
            $sheet->mergeCells("A33:B33");
            $sheet->mergeCells("C33:H33");
            $sheet->mergeCells("A34:B34");

            /**
             * Bar Title
             */
            $sheet->setCellValue('C1', $excelConfig->getTitle());
            $sheet->setCellValue('M1',  $excelConfig->getIdFile());
            $sheet->setTitle($excelConfig->getIdFile());
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('logo');
            $drawing->setDescription('logo');
            if($excelConfig->getLogo() != null){
                $drawing->setPath('assets/excel/'.$excelConfig->getLogo()); 
            }else{
                $drawing->setPath('assets/excel/logoExcel.png');
            }
            // put your path and image here
            $drawing->setCoordinates('A1');
            $drawing->setWidthAndHeight(160, 80);
            $drawing->setOffsetX(10);
            $drawing->setRotation(0);
            $drawing->getShadow()->setVisible(false);
            $drawing->getShadow()->setDirection(45);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());


            /**
             * Title Block 
             */
            $sheet->setCellValue('A8', '1-  DADOS DO FORNECEDOR');
            $sheet->setCellValue('A15', '2 - TERMO DE COMPROMISSO');
            $sheet->setCellValue('A26', '3 - TIPO DE SERVIÇOS');
            $sheet->setCellValue('A30', '4 – AVALIAÇÃO');


            /**
             * Title Data in Block 
             */
            $sheet->setCellValue('B5', 'Avaliação Inicial');
            $sheet->setCellValue('I5', 'Reavaliação');
            $sheet->setCellValue('A6', 'Data da avaliação: ');
            $sheet->setCellValue('D6', '0000-00-00');
            $sheet->setCellValue('A9', 'Nome do Fornecedor:');
            $sheet->setCellValue('A10', 'Endereço:');
            $sheet->setCellValue('J10', 'Telefone:');
            $sheet->setCellValue('A11', 'Cidade:');
            $sheet->setCellValue('J11', 'Estado:');
            $sheet->setCellValue('A12', 'E-mail:');
            $sheet->setCellValue('A13', 'Contato:');
            $sheet->setCellValue('I31', 'Escolha a pontuação conforme a tabela ao lado. Para detalhes da pontuação quanto ao “Prazo de Entrega”, “Qualidade do Atendimento”, “Qualidade do Produto ou Serviço” e “Condições Comerciais”, consulte a IT124 – Avaliação de fornecedores especiais.');
            $sheet->setCellValue('A31', 'Pontuação');
            $sheet->setCellValue('C31', 'Critérios');
            $sheet->setCellValue('A32', '2.5');
            $sheet->setCellValue('C32', 'Atende Totalmente');
            $sheet->setCellValue('A33', '1.0');
            $sheet->setCellValue('C33', 'Atende Parcialmente');
            $sheet->setCellValue('A34', '0.0');
            $sheet->setCellValue('C34', 'Não Atende');
            $sheet->setCellValue('A35', 'Qtde Serviços');
            $sheet->setCellValue('C35', 'Data');
            $sheet->setCellValue('D35', 'Prazo de Entrega');
            $sheet->setCellValue('F35', 'Qualidade Atendimento');
            $sheet->setCellValue('H35', 'Qualidade Produto Serviço');
            $sheet->setCellValue('J35', 'Condições comerciais');
            $sheet->setCellValue('L35', 'Total');
            $sheet->setCellValue('M35', 'Responsável');

            $sheet->setCellValue('E9', $fournisor->getNome());
            $sheet->setCellValue('C10',  $fournisor->getAdress() . ' ' . $fournisor->getNumber());
            $sheet->setCellValue('L10', $fournisor->getPhone());
            $sheet->setCellValue('C11', $fournisor->getCity() . ' ' . $fournisor->getDistrict());
            $sheet->setCellValue('K11', $fournisor->getState());
            $sheet->setCellValue('C12', $fournisor->getEmail());
            $sheet->setCellValue('C13', $fournisor->getContato());
            $sheet->setCellValue('A16', $termo->getDescription());
            $sheet->setCellValue('A27', $services[0]->getTypeService()->getName());

            $numberCol = 36;
            $total = 0;
            foreach ($services as $key => $value) {

                $sheet->mergeCells("A" . $numberCol . ":B" . $numberCol);
                $sheet->mergeCells("D" . $numberCol . ":E" . $numberCol);
                $sheet->mergeCells("F" . $numberCol . ":G" . $numberCol);
                $sheet->mergeCells("H" . $numberCol . ":I" . $numberCol);
                $sheet->mergeCells("J" . $numberCol . ":K" . $numberCol);
                $sheet->mergeCells("M" . $numberCol . ":N" . $numberCol);

                $dataFournisor = array(
                    'font' => array(
                        'italic' => false,
                        'bold' => false,
                        'color' => array('rgb' => '37AA87'),
                        'size'  => 9,
                        'name'  => 'Verdana'
                    )
                );


                $sheet->getStyle('A' . $numberCol . ':N' . $numberCol)->applyFromArray($dataFournisor);
                $sheet->setCellValue('A' . $numberCol, '1');
                $sheet->setCellValue('C' . $numberCol, $value->getDate());
                $sheet->setCellValue('D' . $numberCol, $value->getPrazo());
                $sheet->setCellValue('F' . $numberCol, $value->getQuality());
                $sheet->setCellValue('H' . $numberCol, $value->getQualityProduct());
                $sheet->setCellValue('J' . $numberCol, $value->getCondetionComercial());

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('L' . $numberCol)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('C0C0C0');
                $sheet->setCellValue('L' . $numberCol, $value->getTotal());

                $sheet->setCellValue('M' . $numberCol, $value->getResponsible()->getName());
                $numberCol = $numberCol + 1;
                $total = $total + $value->getTotal();
            }

            $sheet->mergeCells("G" . $numberCol . ":K" . $numberCol);
            $sheet->mergeCells("M" . $numberCol . ":N" . $numberCol);
            $sheet->mergeCells("A" . $numberCol . ":B" . $numberCol);


            $spreadsheet
                ->getActiveSheet()
                ->getStyle('A' . $numberCol)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('C0C0C0');
            $sheet->setCellValue('A' . $numberCol, sizeof($services));
            $sheet->setCellValue('C' . $numberCol, '← TOTAL DE FORNECIMENTOS ');

            $sheet->mergeCells("C" . $numberCol . ":F" . $numberCol);
            $sheet->setCellValue('G' . $numberCol, 'TOTAL DE PONTOS →');

            $sheet->getStyle('G' . $numberCol)->getAlignment()->setHorizontal('right');

            $spreadsheet
                ->getActiveSheet()
                ->getStyle('L' . $numberCol)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('C0C0C0');
            $sheet->setCellValue('L' . $numberCol, $total);


            $numberCol = $numberCol + 1;
            $sheet->mergeCells("A" . $numberCol . ":N" . $numberCol);

            $numberCol = $numberCol + 1;
            $sheet->mergeCells("A" . $numberCol . ":N" . $numberCol);

            $sheet->setCellValue('A' . $numberCol, '5 – RESULTADO DA AVALIAÇÃO');
            $sheet->getStyle('A' . $numberCol)->applyFromArray($TitleBlock);
            $sheet->getStyle('A' . $numberCol)->getAlignment()->setHorizontal('center');
            $spreadsheet
                ->getActiveSheet()
                ->getStyle('A' . $numberCol)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('C0C0C0');


            $numberCol = $numberCol + 1;
            $sheet->mergeCells("A" . $numberCol . ":B" . $numberCol);
            $sheet->setCellValue('A' . $numberCol, 'ID');

            $sheet->mergeCells("C" . $numberCol . ":H" . $numberCol);
            $sheet->setCellValue('A' . $numberCol, 'Resultado');

            $sheet->mergeCells("I" . $numberCol . ":N" . ($numberCol + 2));
            $sheet->setCellValue('I' . $numberCol, 'Desqualificar o fornecedor Se obtiver nota abaixo de 7%');
            $sheet->getStyle('I' . $numberCol)->getAlignment()->setVertical('center');

            $numberCol = $numberCol + 1;
            $sheet->mergeCells("A" . $numberCol . ":B" . $numberCol);
            $sheet->setCellValue('A' . $numberCol, '≥7 %');

            $sheet->mergeCells("C" . $numberCol . ":H" . $numberCol);
            $sheet->setCellValue("C" . $numberCol, 'Fornecedor Qualificado');
            $numberCol = $numberCol + 1;

            $sheet->mergeCells("A" . $numberCol . ":B" . $numberCol);
            $sheet->setCellValue('A' . $numberCol, '≤7 %');

            $sheet->mergeCells("C" . $numberCol . ":H" . $numberCol);
            $sheet->setCellValue("C" . $numberCol, 'Fornecedor Desqualificado');


            $numberCol = $numberCol + 1;
            $sheet->mergeCells("A" . $numberCol . ":D" . $numberCol);
            $sheet->setCellValue('A' . $numberCol, 'ID – ÍNDICE DE DESEMPENHO →');
            $sheet->getStyle('A' . $numberCol)->getAlignment()->setHorizontal('right');
            $sheet->mergeCells("E" . $numberCol . ":F" . $numberCol);
            $sheet->setCellValue('E' . $numberCol, $fournisor->getResult()->getAvreage());
            $sheet->getStyle('E' . $numberCol)->getAlignment()->setHorizontal('left');

            $sheet->mergeCells("G" . $numberCol . ":N" . $numberCol);
            //if fournisors.result.avreage >= 7 
            if ($fournisor->getResult()->getAvreage() >=  7) {
                $sheet->setCellValue('G' . $numberCol, 'Fornecedor Qualificado');

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('G' . $numberCol)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('B0E277');
            } else {
                $sheet->setCellValue('G' . $numberCol, 'Fornecedor Desqualificado');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('G' . $numberCol)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('CD7092');
            }

            $sheet->getStyle('G' . $numberCol)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:N' . $numberCol)->getAlignment()->setWrapText(true);
            // $sheet->getColumnDimension('A')->setWidth(100);
            // $sheet->getRowDimension('2')->setRowHeight(26); 

            return $spreadsheet;
        }
    }
}
