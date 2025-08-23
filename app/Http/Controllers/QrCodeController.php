<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\QrCode;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);

        $restaurant->load('tables.qrCodes');
        $qrCodes = QrCode::where('restaurant_id', $restaurant->id)
            ->with('table')
            ->latest()
            ->get();

        return view('owner.restaurants.qr-codes.index', compact('restaurant', 'qrCodes'));
    }

    public function generate(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'table_ids' => 'required|array',
            'table_ids.*' => 'exists:tables,id',
        ]);

        $qrCodes = [];
        foreach ($validated['table_ids'] as $tableId) {
            $table = $restaurant->tables()->find($tableId);
            if ($table) {
                $qrCodes[] = $this->qrCodeService->generateForTable($restaurant, $table);
            }
        }

        if (request()->headers->get('HX-Request')) {
            return view('partials.qr-codes-list', compact('qrCodes'));
        }

        return redirect()->back()->with('success', count($qrCodes) . ' QR codes generated successfully');
    }

    public function download(Restaurant $restaurant, QrCode $qrCode)
    {
        $this->authorize('view', $restaurant);

        if ($qrCode->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $pdfPath = $this->qrCodeService->generatePrintablePDF($qrCode);
        
        return response()->download(storage_path('app/public/' . $pdfPath))
            ->deleteFileAfterSend(true);
    }
}
