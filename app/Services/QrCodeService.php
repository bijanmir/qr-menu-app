<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeService
{
    public function generateForTable(Restaurant $restaurant, Table $table): QrCode
    {
        // Generate URL for the table
        $url = route('customer.restaurant.table', [
            'restaurant' => $restaurant->slug,
            'table' => $table->code
        ]);

        // Generate QR code image
        $qrCodeImage = QrCodeGenerator::format('png')
            ->size(300)
            ->margin(2)
            ->generate($url);

        // Save to storage
        $filename = "qr-codes/{$restaurant->slug}/table-{$table->code}.png";
        Storage::disk('public')->put($filename, $qrCodeImage);

        // Create or update QR code record
        return QrCode::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'table_id' => $table->id,
            ],
            [
                'url' => $url,
                'image_path' => $filename,
                'version' => QrCode::where('restaurant_id', $restaurant->id)
                    ->where('table_id', $table->id)
                    ->max('version') + 1,
                'active' => true,
            ]
        );
    }

    public function generateBulkForRestaurant(Restaurant $restaurant): array
    {
        $qrCodes = [];
        
        foreach ($restaurant->tables as $table) {
            $qrCodes[] = $this->generateForTable($restaurant, $table);
        }

        return $qrCodes;
    }

    public function generatePrintablePDF(QrCode $qrCode): string
    {
        // Generate a printable PDF with QR code and table information
        $pdf = app('dompdf.wrapper');
        
        $html = view('pdfs.qr-code', [
            'qrCode' => $qrCode,
            'restaurant' => $qrCode->restaurant,
            'table' => $qrCode->table,
        ])->render();
        
        $pdf->loadHTML($html);
        
        $filename = "qr-prints/table-{$qrCode->table->code}-{$qrCode->version}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        
        return $filename;
    }
}
