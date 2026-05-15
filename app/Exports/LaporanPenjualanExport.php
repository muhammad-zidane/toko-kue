<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(
        private string $dari,
        private string $sampai
    ) {}

    public function collection()
    {
        return Order::with(['user', 'orderItems.product'])
            ->whereBetween('created_at', [$this->dari . ' 00:00:00', $this->sampai . ' 23:59:59'])
            ->whereIn('status', ['completed', 'delivered', 'processing', 'shipped'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'No. Pesanan'     => $order->order_number ?? $order->id,
                    'Tanggal'         => $order->created_at->format('d/m/Y H:i'),
                    'Pelanggan'       => $order->user->name ?? '-',
                    'Email'           => $order->user->email ?? '-',
                    'Status'          => ucfirst($order->status),
                    'Total Produk'    => $order->orderItems->count(),
                    'Subtotal'        => $order->orderItems->sum(fn($i) => $i->price * $i->quantity),
                    'Ongkir'          => $order->shipping_cost ?? 0,
                    'Total'           => $order->total_price,
                    'Metode Bayar'    => $order->payment_method ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No. Pesanan', 'Tanggal', 'Pelanggan', 'Email',
            'Status', 'Total Produk', 'Subtotal (Rp)', 'Ongkir (Rp)', 'Total (Rp)', 'Metode Bayar',
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->dari . ' sd ' . $this->sampai;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 18, 'C' => 22, 'D' => 26,
            'E' => 14, 'F' => 14, 'G' => 16, 'H' => 14, 'I' => 16, 'J' => 16,
        ];
    }
}
