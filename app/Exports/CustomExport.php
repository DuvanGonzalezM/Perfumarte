<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;

class CustomExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected Collection $collection;
    protected string $sheetTitle;
    protected array $headings;
    protected $columnFormat;

    public function __construct(Collection $collection, string $sheetTitle = 'Datos', array $headings = [], $columnFormat = null)
    {
        $this->collection = $collection;
        $this->sheetTitle = $sheetTitle;
        $this->headings = $headings;
        $this->columnFormat = $columnFormat;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'EC9F4F',
                    ],
                ],
            ]
        ];
    }

    public function columnFormats(): array
    {
        if ($this->columnFormat) {
            return [
                $this->columnFormat => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ];
        }
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $cellRange = "A1:{$lastColumn}{$lastRow}";
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];

                $sheet->getStyle($cellRange)->applyFromArray($styleArray);
            },
        ];
    }
}
