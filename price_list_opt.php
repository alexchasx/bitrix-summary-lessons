<?php
require 'vendor/autoload.php';
ini_set('memory_limit', '2048M');

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PriceListOpt
{
    private $dir = '/upload/dealer_assortment/price_list_opt/';

    /**
     * Шаблонный файл
     * @var string
     */
    private $fileTemplate = 'price_list_template.xlsx';

    public function __construct()
    {
        if (!is_dir($this->dir) ) {
            mkdir($this->dir, null, true);
        }
    }

    public function generateExcel()
    {
        // вернуться к варианту с макросами (тогда файл читался) + эл.упр.
        // иначе: в ручную сделать шаблон копипастом листов
        // на листе "Прайс-заказ" - фильтры, группировка строк.

        try {
            $obSpreadsheet = IOFactory::createReader("Xlsx")->load($this->fileTemplate);

            $obActiveSheet = $obSpreadsheet->setActiveSheetIndexByName('Прайс-заказ');

            $arData = [
                ['2010', 'Q1', 'United States', 790],
                ['2010', 'Q2', 'United States', 730],
                ['2010', 'Q2', 'United States', 730],
                ['2010', 'Q1', 'Belgium', 380],
                ['2011', 'Q2', 'Belgium', 390],
                ['2011', 'Q2', 'Belgium', 390],
                ['2011', 'Q2', 'Belgium', 390],
                ['2011', 'Q2', 'Belgium', 390],
                ['2011', 'Q2', 'Belgium', 390],
                ['2011', 'Q2', 'Belgium', 390],
            ];
            $obActiveSheet->fromArray($arData, null, 'A2');
            $obActiveSheet->getStyle('A1:D1')->getFont()->setBold(true);

            // Установка фильтров
            $obActiveSheet->setAutoFilter($obSpreadsheet->getActiveSheet()->calculateWorksheetDimension());

            // Группировка строк
            foreach ($arData as $key => $value) {
                if ($key >= 3 && $key <= 6) {
//                    $indexRow = $key + 3;
                    $obActiveSheet->getRowDimension($key)->setOutlineLevel(1);
                    $obActiveSheet->getRowDimension($key)->setVisible(false);
                }
            }
            $obActiveSheet->getRowDimension(6)->setCollapsed(true);

            $writer = new Xlsx($obSpreadsheet);
            $writer->save('price_list_opt.xlsx');

        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}

(new PriceListOpt())->generateExcel();
