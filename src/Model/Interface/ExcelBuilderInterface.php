<?php

namespace App\Model\Interface;

/**
 * The Builder interface specifies methods for creating the different parts of
 * the Product objects.
 */
interface ExcelBuilderInterface
{
    public function barTitle($title): void;
    public function barTitleWithImage(): void;
    public function blockDataFornisor(): void;
    public function blockDataTerms(): void;
    public function blockTypeOfService(): void;
    public function blockReview(): void;
    public function blockResult(): void;
}
