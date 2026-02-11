<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Property;
use App\Models\PropertyImage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::with('images')->get();
        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_type_id' => 'required|integer',
            'property_type_name' => 'required|string',
            'purpose' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'area_sqft' => 'required|integer',
            'bhk_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'toilet_type' => 'nullable|string',
            'furnishing' => 'nullable|string',
            'preferred_tenant' => 'nullable|string',
            'floor_number' => 'nullable|integer',
            'total_floors' => 'nullable|integer',
            'property_age' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'locality' => 'required|string',
            'address_line' => 'required|string',
            'landmark' => 'nullable|string',
            'zip_code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'monthly_rent' => 'required|numeric',
            'security_deposit' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images' => 'nullable|array|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $property = Property::create($request->except('images'));

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('property_images', 'public');
                $propertyImage = PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                ]);
                $imagePaths[] = $path;
            }
        }

        return response()->json([
            'success' => true,
            'property_id' => $property->id,
            'images' => $imagePaths,
        ], 201);
    }
}
