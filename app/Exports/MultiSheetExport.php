<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    protected $dataPerSheet;

    public function __construct(array $dataPerSheet)
    {
        $this->dataPerSheet = $dataPerSheet;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->dataPerSheet as $data) {
            $sheets[] = new CustomExport($data['collection'], $data['title'], $data['headings'], $data['columnFormat']);
        }

        return $sheets;
    }
}
