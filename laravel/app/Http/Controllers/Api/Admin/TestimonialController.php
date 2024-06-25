<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return Testimonial::select('id', 'name', 'type', 'description', 'image', 'created_at', 'updated_at')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Customer,Partner',
            'description' => 'required|string',
            'image' => 'nullable|string',
        ]);

        $testimonial = Testimonial::create($validatedData);

        return response()->json($testimonial, 201);
    }

    public function show($id)
    {
        return Testimonial::select('id', 'name', 'type', 'description', 'image', 'created_at', 'updated_at')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Customer,Partner',
            'description' => 'required|string',
            'image' => 'nullable|string',
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update($validatedData);

        return response()->json($testimonial);
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return response()->json(null, 204);
    }
}
