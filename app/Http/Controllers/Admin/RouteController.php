<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::orderBy('from_location')->orderBy('departure_time')->paginate(15);
        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_time' => 'required',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:1',
        ]);

        Route::create($request->all());

        return redirect()->route('admin.routes.index')->with('success', 'Route created successfully!');
    }

    public function edit($id)
    {
        $route = Route::findOrFail($id);
        return view('admin.routes.edit', compact('route'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_time' => 'required',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $route = Route::findOrFail($id);
        $route->update($request->all());

        return redirect()->route('admin.routes.index')->with('success', 'Route updated successfully!');
    }

    public function destroy($id)
    {
        $route = Route::findOrFail($id);
        $route->delete();

        return redirect()->route('admin.routes.index')->with('success', 'Route deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $route = Route::findOrFail($id);
        $route->is_active = !$route->is_active;
        $route->save();

        return back()->with('success', 'Route status updated successfully!');
    }
}
