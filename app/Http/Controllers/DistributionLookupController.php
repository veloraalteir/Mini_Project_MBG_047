<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\JsonResponse;

class DistributionLookupController extends Controller
{
    public function bySchool(School $school): JsonResponse
    {
        $items = $school->distributions()
            ->orderByDesc('tanggal')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'label' => $d->tanggal->format('d M Y') . ' — ' . $d->menu,
            ]);

        return response()->json($items);
    }
}