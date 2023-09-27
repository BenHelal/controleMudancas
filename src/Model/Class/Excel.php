<?php

namespace App\Model\Class;

use App\Entity\ExcelConfig;
use App\Entity\Process;
use App\Entity\SectorProcess;
use App\Model\Interface\ExcelBuilderInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\XmlConfiguration\IniSetting;

class Excel
{
    public function generateExcel($mudancas, ManagerRegistry $doctrine)
    {
        $spreadsheet  = new Spreadsheet();
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:B3");
        $sheet->mergeCells("C1:E3");
        $sheet->mergeCells("F1:H3");
        $sheet->mergeCells("I1:K3");
        $sheet->mergeCells("L1:N3");
        $sheet->mergeCells("O1:Q3");
        $sheet->mergeCells("R1:T3");
        $sheet->mergeCells("U1:W3");
        $sheet->mergeCells("X1:Z3");
        $sheet->mergeCells("AA1:AC3");
        $sheet->mergeCells("AD1:AF3");
        $sheet->mergeCells("AG1:AI3");
        $sheet->mergeCells("AJ1:AL3");
        $sheet->mergeCells("AM1:AO3");
        $sheet->mergeCells("AP1:AR3");
        $sheet->mergeCells("AS1:AU3");
        $sheet->mergeCells("AV1:AX3");
        $sheet->mergeCells("AY1:BA3");
        $sheet->mergeCells("BB1:BD3");
        $sheet->mergeCells("BE1:BG3");
        $sheet->mergeCells("BH1:BJ3");
        $sheet->mergeCells("BK1:BM3");
        $sheet->mergeCells("BN1:BP3");
        $sheet->mergeCells("BQ1:BS3");
        $sheet->mergeCells("BT1:BV3");
        $sheet->mergeCells("BW1:BY3");
        $sheet->mergeCells("BZ1:CB3");
        $sheet->mergeCells("CC1:CE3");
        $sheet->mergeCells("CF1:CH3");

        $sheet->mergeCells("CI1:CK3");
        $sheet->mergeCells("CL1:CN3");
        $sheet->mergeCells("CO1:CQ3");
        $sheet->mergeCells("CR1:CT3");
        $sheet->mergeCells("CU1:CW3");
        $sheet->mergeCells("CX1:CZ3");

        $sheet->mergeCells("DA1:DC3");
        $sheet->mergeCells("DD1:DF3");
        $sheet->mergeCells("DG1:DI3");
        $sheet->mergeCells("DJ1:DL3");
        $sheet->mergeCells("DM1:DO3");
        $sheet->mergeCells("DP1:DR3");

        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('L1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('O1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('R1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('U1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('X1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AA1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AD1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AG1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AJ1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AM1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AP1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AS1')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('A1', 'Número da Mudança');
        $sheet->setCellValue('C1', 'Nome da Mudança');
        $sheet->setCellValue('F1', 'Descrição da Mudança');

        $sheet->setCellValue('I1', 'Nome do Projeto Nansen');
        $sheet->setCellValue('L1', 'Código Nansen');

        $sheet->setCellValue('O1', 'Justificativa');
        $sheet->setCellValue('R1', 'Descrição do Impacto');
        $sheet->setCellValue('U1', 'Descrição do Impacto na Área do Solicitante');

        $sheet->setCellValue('X1', 'Área Impactada');
        $sheet->setCellValue('AA1', 'Área Responsável pela Mudança');
        $sheet->setCellValue('AD1', 'Escolha o Cliente');
        $sheet->setCellValue('AG1', 'Gestor da Mudança');
        $sheet->setCellValue('AJ1', 'Data Estimada de Início');
        $sheet->setCellValue('AM1', 'Data Estimada de Término');
        $sheet->setCellValue('AP1', 'Data Efetiva de Início');
        $sheet->setCellValue('AS1', 'Custo');
        $sheet->setCellValue('AV1', 'Mudança Implementada');

        $sheet->setCellValue('AY1', 'Tipo de Aprovação');
        $sheet->setCellValue('BB1', 'Área/Cliente');
        $sheet->setCellValue('BE1', 'Nome do Aprovador');
        $sheet->setCellValue('BH1', 'Data da Aprovação');
        $sheet->setCellValue('BK1', 'Status da Aprovação');
        $sheet->setCellValue('BN1', 'Comentário');

        $sheet->setCellValue('BQ1', 'Tipo de Aprovação');
        $sheet->setCellValue('BT1', 'Área/Cliente');
        $sheet->setCellValue('BW1', 'Nome do Aprovador');
        $sheet->setCellValue('BZ1', 'Data da Aprovação');
        $sheet->setCellValue('CC1', 'Status da Aprovação');
        $sheet->setCellValue('CF1', 'Comentário');

        $sheet->setCellValue('CI1', 'Tipo de Aprovação');
        $sheet->setCellValue('CL1', 'Área/Cliente');
        $sheet->setCellValue('CO1', 'Nome do Aprovador');
        $sheet->setCellValue('CR1', 'Data da Aprovação');
        $sheet->setCellValue('CU1', 'Status da Aprovação');
        $sheet->setCellValue('CX1', 'Comentário');

        $sheet->setCellValue('DA1', 'Tipo de Aprovação');
        $sheet->setCellValue('DD1', 'Área/Cliente');
        $sheet->setCellValue('DG1', 'Nome do Aprovador');
        $sheet->setCellValue('DJ1', 'Data da Aprovação');
        $sheet->setCellValue('DM1', 'Status da Aprovação');
        $sheet->setCellValue('DP1', 'Comentário');

        $b = 6;
        $a = 4;

        $arrayAlphabet = [];
        foreach (range("A", "Z") as $elements) {

            // Display all alphabetic elements
            // one after another
            array_push($arrayAlphabet, $elements);
        }
        for ($i = 0; $i < sizeof($mudancas); $i++) {

            $sheet->mergeCells("A" . $a . ":B" . $b);
            $sheet->setCellValue("A" . $a, $mudancas[$i]->getId());

            $sheet->mergeCells("C" . $a . ":E" . $b);
            $sheet->setCellValue("C" . $a, $mudancas[$i]->getNomeMudanca());

            $sheet->mergeCells("F" . $a . ":H" . $b);
            $sheet->setCellValue("F" . $a, $mudancas[$i]->getDescMudanca());

            $sheet->mergeCells("I" . $a . ":K" . $b);
            if ($mudancas[$i]->getNansenName() != null) {
                $sheet->setCellValue("I" . $a, $mudancas[$i]->getNansenName());
            } else {
                $sheet->setCellValue("I" . $a, "Nenhum dado disponível");
            }

            $sheet->mergeCells("L" . $a . ":N" . $b);
            if ($mudancas[$i]->getNansenName() != null) {
                $sheet->setCellValue("L" . $a, $mudancas[$i]->getNansenNumber());
            } else {
                $sheet->setCellValue("L" . $a, "Nenhum dado disponível");
            }

            $sheet->mergeCells("O" . $a . ":Q" . $b);
            $sheet->setCellValue("O" . $a, $mudancas[$i]->getJustif());

            $sheet->mergeCells("R" . $a . ":T" . $b);
            $sheet->setCellValue("R" . $a, $mudancas[$i]->getDescImpacto());

            $sheet->mergeCells("U" . $a . ":W" . $b);
            $sheet->setCellValue("U" . $a, $mudancas[$i]->getDescImpactoArea());

            $sheet->mergeCells("X" . $a . ":Z" . $b);

            $areaImpact = " ";
            foreach ($mudancas[$i]->getAreaImpact() as $key => $value) {
                $areaImpact = $areaImpact . ", " . $value->getName();
            }
            $sheet->setCellValue("X" . $a, $areaImpact);


            /**
             * $sheet->setCellValue('AA1', 'Área Responsável pela Mudança');
             * */


            $sheet->mergeCells("AA" . $a . ":AC" . $b);
            $sheet->setCellValue("AA" . $a, $mudancas[$i]->getAreaResp()->getName());

            /*
             * $sheet->setCellValue('AD1', 'Escolha o Cliente');
             */

            $sheet->mergeCells("AD" . $a . ":AF" . $b);
            if ($mudancas[$i]->getClient() != null) {
                $sheet->setCellValue("AD" . $a, $mudancas[$i]->getNansenNumber());
            } else {
                $sheet->setCellValue("AD" . $a, "Nenhum dado disponível");
            }

            /*
            * $sheet->setCellValue('AG1', 'Gestor da Mudança');*/
            $sheet->mergeCells("AG" . $a . ":AI" . $b);
            if ($mudancas[$i]->getMangerMudancas() != null) {
                $sheet->setCellValue("AG" . $a, $mudancas[$i]->getMangerMudancas()->getName());
            } else {
                $sheet->setCellValue("AG" . $a, "Nenhum dado disponível");
            }

            /*
            * $sheet->setCellValue('AJ1', 'Data Estimada de Início');*/
            $sheet->mergeCells("AJ" . $a . ":AL" . $b);
            $sheet->setCellValue("AJ" . $a, $mudancas[$i]->getStartMudancas());

            /** $sheet->setCellValue('AM1', 'Data Estimada de Término');*/
            $sheet->mergeCells("AM" . $a . ":AO" . $b);
            $sheet->setCellValue("AM" . $a, $mudancas[$i]->getEndMudancas());

            /*** $sheet->setCellValue('AP1', 'Data Efetiva de Início'); */
            $sheet->mergeCells("AP" . $a . ":AR" . $b);
            $sheet->setCellValue("AP" . $a, $mudancas[$i]->getEffictiveStartDate());

            /*** $sheet->setCellValue('AS1', 'Custo');*/
            $sheet->mergeCells("AS" . $a . ":AU" . $b);
            $sheet->setCellValue("AS" . $a, $mudancas[$i]->getCost());

            /*** $sheet->setCellValue('AV1', 'Mudança Implementada'); */
            $sheet->mergeCells("AV" . $a . ":AX" . $b);
            $sheet->setCellValue("AV" . $a, $mudancas[$i]->getImplemented());

            /*** $sheet->setCellValue('AY1', 'Tipo de Aprovação'); */
            $sheet->mergeCells("AY" . $a . ":BA" . $b);
            $sheet->setCellValue("AY" . $a, "Solicitante");

            /** $sheet->setCellValue('BB1', 'Área/Cliente'); */
            $sheet->mergeCells("BB" . $a . ":BD" . $b);
            $sheet->setCellValue("BB" . $a, $mudancas[$i]->getAddBy()->getFunction()->getName());

            /*** $sheet->setCellValue('BD1', 'Nome do Aprovador'); */
            $sheet->mergeCells("BE" . $a . ":BG" . $b);
            $sheet->setCellValue("BE" . $a, $mudancas[$i]->getAddBy()->getName());

            $sheet->mergeCells("BH" . $a . ":BJ" . $b);
            $sheet->setCellValue("BH" . $a, strval($mudancas[$i]->getDataCreation()));

            $sheet->mergeCells("BK" . $a . ":BM" . $b);
            $sheet->setCellValue("BK" . $a, "N.A");

            $sheet->mergeCells("BN" . $a . ":BP" . $b);
            $sheet->setCellValue("BN" . $a, "N.A");
            //
            $sheet->mergeCells("BQ" . $a . ":BS" . $b);
            $sheet->setCellValue("BQ" . $a, "Gerente Solicitante");

            //$mudancas[$i]->getAddBy()->getFunction()->getManager()->getName()
            $sheet->mergeCells("BT" . $a . ":BV" . $b);
            $sheet->setCellValue("BT" . $a, $mudancas[$i]->getAddBy()->getFunction()->getName());

            $sheet->mergeCells("BW" . $a . ":BY" . $b);
            $sheet->setCellValue("BW" . $a, $mudancas[$i]->getAddBy()->getFunction()->getManager()->getName());


            $sheet->mergeCells("BZ" . $a . ":CB" . $b);
            $sheet->setCellValue("BZ" . $a, $mudancas[$i]->getDateMUA());


            $sheet->mergeCells("CC" . $a . ":CE" . $b);
            if (
                $mudancas[$i]->getManagerUserApp() == 1 &&
                $mudancas[$i]->getManagerUserAdd() != null &&
                $mudancas[$i]->getManagerUserAdd() != $mudancas[$i]->getAddBy() &&
                $mudancas[$i]->getManagerUserAdd() != null
            ) {
                $sheet->setCellValue("CC" . $a, "Aprovado");
            } elseif ($mudancas[$i]->getManagerUserApp() == 1 && ($mudancas[$i]->getManagerUserAdd() == $mudancas[$i]->getAddBy()  ||  $mudancas[$i]->getManagerUserAdd() == null)) {
                $sheet->setCellValue("CC" . $a, "Aprovação automática");
            } elseif ($mudancas[$i]->getManagerUserApp() == 2) {
                $sheet->setCellValue("CC" . $a, "Reprovado");
            } else {
                $sheet->setCellValue("CC" . $a, "Não verificado");
            }

            $sheet->mergeCells("CF" . $a . ":CH" . $b);
            $sheet->setCellValue("CF" . $a, $mudancas[$i]->getManagerUserComment());


            /********************
        $sheet->mergeCells("CI1:CK3");
        $sheet->mergeCells("CL1:CN3");
        $sheet->mergeCells("CO1:CQ3");
        $sheet->mergeCells("CR1:CT3");
        $sheet->mergeCells("CU1:CW3");
        $sheet->mergeCells("CX1:CZ3");*********************************** */
            $sheet->mergeCells("CI" . $a . ":CK" . $b);
            $sheet->setCellValue("CI" . $a, "Gerente Aprovação");

            //$mudancas[$i]->getAddBy()->getFunction()->getManager()->getName()
            $sheet->mergeCells("CL" . $a . ":CN" . $b);
            $sheet->setCellValue("CL" . $a, $mudancas[$i]->getAreaResp()->getName());

            $sheet->mergeCells("CO" . $a . ":CQ" . $b);
            $sheet->setCellValue("CO" . $a, $mudancas[$i]->getAreaResp()->getManager()->getName());


            $sheet->mergeCells("CR" . $a . ":CT" . $b);
            $sheet->setCellValue("CR" . $a, $mudancas[$i]->getDateAM());


            $sheet->mergeCells("CU" . $a . ":CW" . $b);
            if (
                $mudancas[$i]->getAppMan() == 1
            ) {
                $sheet->setCellValue("CU" . $a, "Aprovado");
            } elseif ($mudancas[$i]->getAppMan() == 2) {
                $sheet->setCellValue("CU" . $a, "Reprovado");
            } else {
                $sheet->setCellValue("CU" . $a, "Não verificado");
            }

            $sheet->mergeCells("CX" . $a . ":CZ" . $b);
            $sheet->setCellValue("CX" . $a, $mudancas[$i]->getManagerUserComment());


            /********************        
             *  $sheet->mergeCells("DA1:DC3");
                $sheet->mergeCells("DD1:DF3");
                $sheet->mergeCells("DG1:DI3");
                $sheet->mergeCells("DJ1:DL3");
                $sheet->mergeCells("DM1:DO3");
                $sheet->mergeCells("DP1:DR3");*********************************** */
            $sheet->mergeCells("DA" . $a . ":DC" . $b);
            $sheet->setCellValue("DA" . $a, "Gestor da Mudança");

            //$mudancas[$i]->getAddBy()->getFunction()->getManager()->getName()
            $sheet->mergeCells("DD" . $a . ":DF" . $b);
            if ($mudancas[$i]->getMangerMudancas() != null) {
                $sheet->setCellValue("DD" . $a, $mudancas[$i]->getMangerMudancas()->getName());
            } else {
                $sheet->setCellValue("DD" . $a, "Não Dados");
            }
            $sheet->mergeCells("DG" . $a . ":DI" . $b);
            if ($mudancas[$i]->getMangerMudancas() != null) {
                $sheet->setCellValue("DG" . $a, $mudancas[$i]->getMangerMudancas()->getFunction()->getName());
            } else {
                $sheet->setCellValue("DG" . $a, "Não Dados");
            }

            $sheet->mergeCells("DJ" . $a . ":DL" . $b);
            $sheet->setCellValue("DJ" . $a, $mudancas[$i]->getDateAG());


            $sheet->mergeCells("DM" . $a . ":DO" . $b);
            if (
                $mudancas[$i]->getAppGest() == 1
            ) {
                $sheet->setCellValue("DM" . $a, "Aprovado");
            } elseif ($mudancas[$i]->getAppGest() == 2) {
                $sheet->setCellValue("DM" . $a, "Reprovado");
            } else {
                $sheet->setCellValue("DM" . $a, "Não verificado");
            }

            $sheet->mergeCells("DP" . $a . ":DR" . $b);
            $sheet->setCellValue("DP" . $a, $mudancas[$i]->getComGest());




            $lastArrayIndex1 = 'D';
            $lastArrayIndex22 = 'R';

            $lastArrayIndex = 'DR';
            $lastArrayIndex2 = 'DR';

            foreach ($mudancas[$i]->getAreaImpact() as $key => $value) {


                $in = array_search($lastArrayIndex22, $arrayAlphabet);
                $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                if ($in + 1 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                if ($in + 2 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }


                $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                $sheet->setCellValue($lastArrayIndex . '1', 'Tipo de Aprovação');
                $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                $sheet->setCellValue($lastArrayIndex . $a, 'Área Impactada');


                $in = array_search($lastArrayIndex2[1], $arrayAlphabet);
                $in0 = array_search($lastArrayIndex2[0], $arrayAlphabet);

                $lastArrayIndex = $lastArrayIndex2;
                $lastArrayIndex2 = $lastArrayIndex2;

                if ($in + 1 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                if ($in + 2 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                
                $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                $sheet->setCellValue($lastArrayIndex . '1', 'Área/Cliente');
                $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                $sheet->setCellValue($lastArrayIndex . $a, $value->getName());


                $in = array_search($lastArrayIndex2[1], $arrayAlphabet);
                $in0 = array_search($lastArrayIndex2[0], $arrayAlphabet);

                $lastArrayIndex = $lastArrayIndex2;
                $lastArrayIndex2 = $lastArrayIndex2;

                if ($in + 1 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                if ($in + 2 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }
                $em = $doctrine->getManager();
                $proc = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mudancas[$i]]);
                $procSec2 = $em->getRepository(SectorProcess::class)->findAll(['process' => $proc]);
                $procSec = null;
                if ($procSec2 != null) {
                    
                    foreach ($procSec2 as $key => $value2) {
                        if ($value2->getSector() == $value) {
                            $procSec = $value2;
                        }
                    }
                }


                $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                $sheet->setCellValue($lastArrayIndex . '1', 'Nome do Aprovador');
                $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                if($procSec->getPerson() != null){
                    $sheet->setCellValue($lastArrayIndex . $a, $procSec->getPerson()->getName());
                }


                

                $in = array_search($lastArrayIndex2[1], $arrayAlphabet);
                $in0 = array_search($lastArrayIndex2[0], $arrayAlphabet);

                $lastArrayIndex = $lastArrayIndex2;
                $lastArrayIndex2 = $lastArrayIndex2;

                if ($in + 1 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                if ($in + 2 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }


                $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                $sheet->setCellValue($lastArrayIndex . '1', 'Data da Aprovação');
                $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                if ($procSec != null) {

                    $sheet->setCellValue($lastArrayIndex . $a, strval($procSec->getDataCreation()));
                } else {

                    $sheet->setCellValue($lastArrayIndex . $a, "Não dados");
                }

                $in = array_search($lastArrayIndex2[1], $arrayAlphabet);
                $in0 = array_search($lastArrayIndex2[0], $arrayAlphabet);

                $lastArrayIndex = $lastArrayIndex2;
                $lastArrayIndex2 = $lastArrayIndex2;

                if ($in + 1 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                if ($in + 2 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }


                $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                $sheet->setCellValue($lastArrayIndex . '1', 'Status da Aprovação');
                $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                if ($procSec != null) {
                    if ($procSec->getAppSectorMan() == 1) {

                        $sheet->setCellValue($lastArrayIndex . $a, "Aprovado");
                    } elseif ($procSec->getAppSectorMan() == 2) {
                        $sheet->setCellValue($lastArrayIndex . $a, "Reprovado");
                    } else {
                        $sheet->setCellValue($lastArrayIndex . $a, "Não verificado");
                    }
                } else {
                    $sheet->setCellValue($lastArrayIndex . $a, "Não dados");
                }

                $in = array_search($lastArrayIndex2[1], $arrayAlphabet);
                $in0 = array_search($lastArrayIndex2[0], $arrayAlphabet);

                $lastArrayIndex = $lastArrayIndex2;
                $lastArrayIndex2 = $lastArrayIndex2;

                if ($in + 1 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                    $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }

                if ($in + 2 > 25) {
                    $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                    $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                    if ($in0 + 1 > 25) {
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                    }
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                } else {
                    $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                    $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                    $in = array_search($lastArrayIndex22, $arrayAlphabet);
                    $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                }


                $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                $sheet->setCellValue($lastArrayIndex . '1', 'Comentário');
                $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);

                if ($procSec != null) {
                    $sheet->setCellValue($lastArrayIndex . $a, $procSec->getComment());
                } else {
                    $sheet->setCellValue($lastArrayIndex . $a, "Não dados");
                }
                
                $in = array_search($lastArrayIndex2[1], $arrayAlphabet);
                $in0 = array_search($lastArrayIndex2[0], $arrayAlphabet);

                $lastArrayIndex = $lastArrayIndex2;
                $lastArrayIndex2 = $lastArrayIndex2;
            }

            if ($mudancas[$i]->getImplemented() != null) {
                $j = 0;
                while ($j <= 5) {
                    if ($in + 1 > 25) {
                        $lastArrayIndex22 = $arrayAlphabet[($in + 1) - 26];
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                        if ($in0 + 1 > 25) {
                            $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                        }
                        $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                        $in = array_search($lastArrayIndex22, $arrayAlphabet);
                        $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                    } else {
                        $lastArrayIndex22 = $arrayAlphabet[$in + 1];
                        $lastArrayIndex = $lastArrayIndex1 . $lastArrayIndex22;
                        $in = array_search($lastArrayIndex22, $arrayAlphabet);
                        $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                    }
        
                    if ($in + 2 > 25) {
                        $lastArrayIndex22 = $arrayAlphabet[($in + 2) - 26];
                        $lastArrayIndex1 = $arrayAlphabet[($in0 + 1)];
                        if ($in0 + 1 > 25) {
                            $lastArrayIndex1 = $arrayAlphabet[($in0 + 1) - 26];
                        }
                        $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                        $in = array_search($lastArrayIndex22, $arrayAlphabet);
                        $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                    } else {
                        $lastArrayIndex22 = $arrayAlphabet[$in + 2];
                        $lastArrayIndex2 = $lastArrayIndex1 . $lastArrayIndex22;
                        $in = array_search($lastArrayIndex22, $arrayAlphabet);
                        $in0 = array_search($lastArrayIndex1, $arrayAlphabet);
                    }
    
                    if ($j == 0) {
                        #  ...
                        $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                        $sheet->setCellValue($lastArrayIndex . '1', 'Tipo de Aprovação');
                        $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                        $sheet->setCellValue($lastArrayIndex . $a, 'Dados de implementação');
                    }elseif ($j == 1) {
                        $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                        $sheet->setCellValue($lastArrayIndex . '1', 'Área/Cliente');
                        $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                        $sheet->setCellValue($lastArrayIndex . $a, $mudancas[$i]->getMangerMudancas()->getFunction()->getName());   
                    }elseif( $j == 2){
                       $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                       $sheet->setCellValue($lastArrayIndex . '1', 'Nome do Aprovador');
                       $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                       $sheet->setCellValue($lastArrayIndex . $a, $mudancas[$i]->getMangerMudancas()->getName());   
                    }elseif( $j == 3){
                        $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                        $sheet->setCellValue($lastArrayIndex . '1', 'Data da Aprovação');
                        $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                        $sheet->setCellValue($lastArrayIndex . $a, $mudancas[$i]->getDateOfImp());   
                    }elseif($j == 4 ){
                        $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                        $sheet->setCellValue($lastArrayIndex . '1', 'Status Aprovação');
                        $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                        if($mudancas[$i]->getImplemented()=="1"){
                            $sheet->setCellValue($lastArrayIndex . $a , "Implementada");
                        }elseif($mudancas[$i]->getImplemented()=="2"){
                            $sheet->setCellValue($lastArrayIndex . $a , "Não implementado");    
                        }else{
                            $sheet->setCellValue($lastArrayIndex . $a , "Dados não disponíveis");    
                        }
                    }elseif ($j == 5) {
                        $sheet->mergeCells($lastArrayIndex . '1:' . $lastArrayIndex2 . '3');
                        $sheet->setCellValue($lastArrayIndex . '1', 'Comentário');
                        $sheet->mergeCells($lastArrayIndex . $a . ':' . $lastArrayIndex2 . $b);
                        $sheet->setCellValue($lastArrayIndex . $a, $mudancas[$i]->getImpDesc());
                    }      
                    $j = $j+1;
                }
            }

            $a = $a + 3;
            $b = $b + 3;
        }

        return $spreadsheet;
    }
}
