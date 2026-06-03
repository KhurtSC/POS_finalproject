<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportExport
{
    public function __construct(
        private string $from,
        private string $to
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        $this->buildSummarySheet($spreadsheet->getActiveSheet());
        $spreadsheet->createSheet();
        $this->buildTopProductsSheet($spreadsheet->getSheet(1));
        $spreadsheet->createSheet();
        $this->buildDailySheet($spreadsheet->getSheet(2));

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "report_{$this->from}_to_{$this->to}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function buildSummarySheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('Summary');

        $query = Sale::completed()
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to);

        $totalSales   = $query->count();
        $totalRevenue = $query->sum('total_amount');

        $this->styleHeader($sheet, 'A1:B1');
        $sheet->fromArray([['Metric', 'Value']], null, 'A1');
        $sheet->fromArray([
            ['Period',             "{$this->from} to {$this->to}"],
            ['Total Transactions', $totalSales],
            ['Total Revenue',      number_format($totalRevenue, 2)],
        ], null, 'A2');

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
    }

    private function buildTopProductsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('Top Products');

        $this->styleHeader($sheet, 'A1:D1');
        $sheet->fromArray([['Rank', 'Product', 'Units Sold', 'Revenue']], null, 'A1');

        $products = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('sale', fn ($q) =>
                $q->completed()
                  ->whereDate('created_at', '>=', $this->from)
                  ->whereDate('created_at', '<=', $this->to)
            )
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $row = 2;
        foreach ($products as $i => $item) {
            $sheet->fromArray([
                $i + 1,
                $item->product_name,
                $item->total_qty,
                number_format($item->total_revenue, 2),
            ], null, "A{$row}");
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
    }

    private function buildDailySheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('Daily Breakdown');

        $this->styleHeader($sheet, 'A1:C1');
        $sheet->fromArray([['Date', 'Transactions', 'Revenue']], null, 'A1');

        $daily = Sale::completed()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'desc')
            ->get();

        $row = 2;
        foreach ($daily as $item) {
            $sheet->fromArray([
                $item->date,
                $item->transactions,
                number_format($item->revenue, 2),
            ], null, "A{$row}");
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
    }

    private function styleHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D9488']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
    }
}