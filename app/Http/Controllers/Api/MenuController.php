<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Http\Resources\MenuResource;

class MenuController extends Controller
{
    /**
     * Get all available menus for the mobile app.
     */
    public function index()
    {
        // Get all available menus
        $menus = Menu::where('is_available', true)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Menus retrieved successfully',
            'data' => MenuResource::collection($menus)
        ]);
    }

    /**
     * Get a specific menu by ID.
     */
    public function show($id)
    {
        $menu = Menu::where('is_available', true)->find($id);

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Menu retrieved successfully',
            'data' => new MenuResource($menu)
        ]);
    }
}
