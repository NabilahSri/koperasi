<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected $data;
    protected $headings;

    public function __construct(array $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastColumnIndex = count($this->headings);
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex);
                $cellRange = 'A1:' . $lastColumn . (count($this->data) + 1);

                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
    
                // Loop through each cell in the range to apply borders and formatting
                for ($col = 1; $col <= $lastColumnIndex; $col++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    
                    for ($row = 1; $row <= count($this->data) + 1; $row++) {
                        // Apply a different style to header cells (row 1)
                        $headerStyle = ($row == 1) ? [
                            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCCCCC']],
                            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                        ] : [];
    
                        // Format numerical values as Indonesian Rupiah for specific columns (Columns starting from E - index 5)
                        // Assuming columns A-D are fixed (No, No Anggota, Nama, Alamat)
                        $format = ($col >= 5 && $row > 1) ? '_-"Rp"* #,##0_-;[Red]-"Rp"* #,##0_-' : null;
    
                        $event->sheet->getStyle($column . $row)->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['rgb' => '575757'],
                                ],
                            ],
                        ] + $headerStyle);
    
                        if ($format) {
                            $event->sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode($format);
                        }
                    }
                }
            }
        ];
    }
    
}


