<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = Property::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', (int)$request->bedrooms);
        }
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', (int)$request->bathrooms);
        }
        if ($request->filled('storeys')) {
            $query->where('storeys', (int)$request->storeys);
        }
        if ($request->filled('garages')) {
            $query->where('garages', (int)$request->garages);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float)$request->price_min * 100);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float)$request->price_max * 100);
        }

        return response()->json($query->get());
    }
}
