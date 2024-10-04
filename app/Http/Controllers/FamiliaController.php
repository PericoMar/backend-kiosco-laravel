<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

class FamiliaController extends Controller
{
    public function index(): JsonResponse
    {
        $families = Familia::getFamilies();
        return response()->json($families);
    }

    public function store(Request $request)
    {
        $familia = Familia::create($request->all());
        return response()->json($familia, 201);
    }

    public function show($id)
    {
        $familia = Familia::findOrFail($id);
        return response()->json($familia);
    }

    public function update(Request $request, $id)
    {
        $familia = Familia::findOrFail($id);
        $familia->update($request->all());
        return response()->json($familia);
    }

    public function destroy($id)
    {
        $familia = Familia::findOrFail($id);
        $familia->delete();
        return response()->json(null, 204);
    }
}
