<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role->name === 'super_admin') {
            $areas = Area::with('school')->orderBy('type')->orderBy('name')->get();
        } else {
            $areas = Area::where('school_id', $user->school_id)
                ->orderBy('type')
                ->orderBy('name')
                ->get();
        }

        return response()->json($areas);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|string|in:classroom,laboratory,workshop,music_room,computer_lab,art_room',
            'equipment' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['school_id'] = $user->school_id;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $area = Area::create($validated);

        return response()->json($area, 201);
    }

    public function show(Area $area): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'super_admin' && $area->school_id !== $user->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($area);
    }

    public function update(Request $request, Area $area): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'super_admin' && $area->school_id !== $user->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'sometimes|string|in:classroom,laboratory,workshop,music_room,computer_lab,art_room',
            'equipment' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $area->update($validated);

        return response()->json($area);
    }

    public function destroy(Area $area): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'super_admin' && $area->school_id !== $user->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $area->delete();

        return response()->json(['message' => 'Area deleted successfully']);
    }
}

