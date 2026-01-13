<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class FilteredDataExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected $data;
    protected $month;

    public function __construct(array $data, $month)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Laporan Data Koprasi Bulan ' . $this->month;
    }

    public function headings(): array
    {
        // Sesuaikan dengan judul kolom yang diinginkan
        $headings = [
            'No',
            'No Anggota',
            'Nama',
            'Alamat',
            'Iuran Wajib',
            'Iuran Pokok',
            'Jumlah Simpanan',
            'Pinjaman',
            'Bagi Hasil',
            'Jumlah Tagihan',
        ];

        return $headings;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:J' . (count($this->data) + 1); // Adjusted range based on the number of rows in the data
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
    
                // Loop through each cell in the range to apply borders and formatting
                foreach (range('A', 'J') as $column) {
                    for ($row = 1; $row <= count($this->data) + 1; $row++) {
                        // Apply a different style to header cells (row 1)
                        $headerStyle = ($row == 1) ? [
                            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCCCCC']],
                            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                        ] : [];
    
                        // Format numerical values as Indonesian Rupiah for specific columns
                        $currencyColumns = ['E', 'F', 'G', 'H', 'I', 'J'];
                        $format = (in_array($column, $currencyColumns) && $row > 1) ? '_-"Rp"* #,##0_-;[Red]-"Rp"* #,##0_-' : null;
    
                        $event->sheet->getStyle($column . $row)->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Adjusted thickness
                                    'color' => ['rgb' => '575757'], // Adjusted color
                                ],
                            ],
                        ] + $headerStyle); // Merge the styles
    
                        // Apply custom number format if applicable
                        if ($format) {
                            $event->sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode($format);
                        }
                    }
                }
            }
        ];
    }
    
}
