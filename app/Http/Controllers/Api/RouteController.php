<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::where('is_active', true);

        if ($request->has('from') && $request->from) {
            $query->where('from_location', 'like', "%{$request->from}%");
        }

        if ($request->has('to') && $request->to) {
            $query->where('to_location', 'like', "%{$request->to}%");
        }

        $routes = $query->orderBy('from_location')->orderBy('departure_time')->get();

        return response()->json($routes);
    }

    public function show($id)
    {
        $route = Route::where('is_active', true)->findOrFail($id);
        return response()->json($route);
    }
}
