<?php

namespace App\Model\Class;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Bar;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

class Odp {
    function odp($data, $title, $results){
        
        
        // dd($results);

        $objPHPPowerPoint = new PhpPresentation();
        // Create slide
        $currentSlide = $objPHPPowerPoint->getActiveSlide();
        // Create a shape (drawing)
        $shape = $currentSlide->createDrawingShape();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath('assets/odp/logoOdp.png')
            ->setHeight(100)
            ->setOffsetX(10)
            ->setOffsetY(10);
        $shape->getShadow()->setVisible(false)
            ->setDirection(45)
            ->setDistance(10);

        // Create a shape (text)
        $shape = $currentSlide->createRichTextShape()
            ->setHeight(300)
            ->setWidth(600)
            ->setOffsetX(170)
            ->setOffsetY(180);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun('Estatística do provedor Serdia !');
        $textRun->getFont()->setBold(true)
            ->setSize(60)
            ->setColor(new Color('FF008B8B'));



        $currentSlide = $objPHPPowerPoint->getActiveSlide();

        /********************************************************** */

        $currentSlide = $objPHPPowerPoint->createSlide();
        $currentSlide->setName('Qualified and Desqualified Fornecedor');
        // Create templated slide

        // Generate sample data for first chart
        $series1Data = [$title[0] => $results[0]];
        $series2Data = [$title[1] => $results[1]];
        $series3Data = [$title[2] => $results[2]];
        //$series2Data = ['Jan' => 266, 'Feb' => 198, 'Mar' => 271, 'Apr' => 305, 'May' => 267, 'Jun' => 301, 'Jul' => 340, 'Aug' => 326, 'Sep' => 344, 'Oct' => 364, 'Nov' => 383, 'Dec' => 379];

        // Create a bar chart (that should be inserted in a shape)
        $barChart = new Bar();
        $barChart->setGapWidthPercent(158);

        $series1 = new Series($title[0], $series1Data);
        $series1->setShowSeriesName(true);
        $series1->getFont()->getColor()->setRGB('000000');
        $series1->getFill()->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FF008B8B'));
        //setStartColor(new Color(Color::COLOR_DARKBLUE/*$theme->getWarrningColor()*/));
        //$series1->getDataPointFill(2)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($theme->getWarrningColor()));
        $series1->setLabelPosition(Series::LABEL_INSIDEEND);

        $series2 = new Series($title[1], $series2Data);
        $series2->setShowSeriesName(true);
        $series2->getFont()->getColor()->setRGB('000000');
        $series2->getFill()->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFE0FFFF'));
        //setStartColor(new Color(Color::COLOR_DARKGREEN/*$theme->getValidColor()*/));
        $series2->setLabelPosition(Series::LABEL_INSIDEEND);

        $series3 = new Series($title[2], $series3Data);
        $series3->setShowSeriesName(true);
        $series3->getFont()->getColor()->setRGB('000000');
        $series3->getFill()->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFFFA500'));
        //setStartColor(new Color(Color::COLOR_DARKYELLOW/*$theme->getErrorColor()*/));
        $series3->setLabelPosition(Series::LABEL_INSIDEEND);


        $barChart->addSeries($series1);
        $barChart->addSeries($series2);
        $barChart->addSeries($series3);


        // Create a shape (chart)
        $shape = $currentSlide->createChartShape();

        $shape->setName('Estatística do provedor Sérdia')
            ->setResizeProportional(false)
            ->setHeight(550)
            ->setWidth(700)
            ->setOffsetX(120)
            ->setOffsetY(80);
        $shape->getBorder()->setLineStyle(Border::LINE_SINGLE);
        $shape->getTitle()->setText('Qualified and Desqualified Fornecedor');
        $shape->getTitle()->getFont()->setItalic(true);
        $shape->getTitle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getPlotArea()->getAxisX()->setTitle('Tipo de informação');
        $shape->getPlotArea()->getAxisY()->getFont()->getColor()->setRGB('FF4F81BD');
        $shape->getPlotArea()->getAxisY()->setTitle('Número');
        $shape->getPlotArea()->setType($barChart);
        $shape->getLegend()->getBorder()->setLineStyle(Border::LINE_SINGLE);
        $shape->getLegend()->getFont()->setItalic(true);
        $shape->getFill()
            ->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(270)
            ->setStartColor(new Color(Color::COLOR_WHITE))
            ->setEndColor(new Color(Color::COLOR_WHITE));

            return $objPHPPowerPoint;
    }
}