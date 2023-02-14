<?php
namespace App\Model\Class;

use App\Model\Interface\ExcelBuilderInterface;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * 
     */
    public function setBuilder(ExcelBuilderInterface $builder): void
    {
        $this->builder = $builder;
    }

    public function buildWithImage():void
    {
        $spreadsheet  = new Spreadsheet();
        $this->builder->barTitle('test',$spreadsheet);
        $this->builder->generateExcel($spreadsheet);
    }

    public function buildFullFeaturedExcel():void
    {
        $spreadsheet  = new Spreadsheet();
        $this->builder->barTitle('test',$spreadsheet);
        $this->builder->barTitleWithImage($spreadsheet);
        $writer = $this->builder->generateExcel();
         // In this case, we want to write the file in the public directory
         $publicDirectory = $this->getParameter('kernel.project_dir');
         // e.g /var/www/project/public/my_first_excel_symfony4.xlsx
         $excelFilepath =  $publicDirectory . '/my_first_excel_symfony4.xlsx';

         // Create the file
         $writer->save($excelFilepath);
    }
}