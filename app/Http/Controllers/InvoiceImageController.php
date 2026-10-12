<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceImageController extends Controller
{
    public function show(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        abort_unless($user, 401);

        // Solo el administrador o el propietario pueden ver la imagen.
        abort_unless(
            $user->role === 'admin' ||
            (int) $invoice->user_id === (int) $user->id,
            403
        );

        abort_unless(
            $invoice->image_path &&
            Storage::disk('local')->exists($invoice->image_path),
            404
        );

        return Storage::disk('local')->response(
            $invoice->image_path,
            null,
            [
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}