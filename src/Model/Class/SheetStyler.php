<?php

namespace App\Model\Class;


class SheetStyler {

private $sheet;

public function __construct($sheet) {
    $this->sheet = $sheet;
}

public function mergeCellsInRange($startCol, $endCol, $rows = 3) {
    for ($i = $startCol; $i <= $endCol; $i+=3) {
        $firstCell = $this->getColumnName($i) . '1';
        $lastCell = $this->getColumnName($i+2) . $rows;
        $this->sheet->mergeCells("$firstCell:$lastCell");
    }
}

public function setWrapTextForRange($startCol, $endCol) {
    for ($i = $startCol; $i <= $endCol; $i+=3) {
        $cell = $this->getColumnName($i) . '1';
        $this->sheet->getStyle($cell)->getAlignment()->setWrapText(true);
    }
}

public function setCellValueForRange($startCol, $endCol, $values) {
    $index = 0;
    for ($i = $startCol; $i <= $endCol && $index < count($values); $i+=3) {
        $cell = $this->getColumnName($i) . '1';
        $this->sheet->setCellValue($cell, $values[$index]);
        $index++;
    }
}

private function getColumnName($index) {
    $dividend = $index + 1;
    $columnName = '';

    while ($dividend > 0) {
        $modulo = ($dividend - 1) % 26;
        $columnName = chr(65 + $modulo) . $columnName;
        $dividend = (int)(($dividend - $modulo) / 26);
    }

    return $columnName;
}
}