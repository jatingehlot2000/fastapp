<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use App\Models\PropertyImage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::with('images')->get();
        
        $properties->each(function($property) {
            $property->images->each(function($image) {
                $image->image_url = 'https://test.teerthsewanyas.org/' . $image->image_path;
            });
        });
        
        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Property submission request:', $request->all());

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
                Log::error('Property validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $propertyData = $request->except('images');
            $propertyData['user_id'] = $request->user()->id;

            $property = Property::create($propertyData);
            Log::info('Property created with ID: ' . $property->id);

            $imagePaths = [];
            if ($request->hasFile('images')) {
                $uploadPath = public_path('property_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move($uploadPath, $filename);
                    $imagePath = 'property_images/' . $filename;

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $imagePath,
                    ]);
                    $imagePaths[] = 'https://test.teerthsewanyas.org/' . $imagePath;
                }
                Log::info('Images uploaded: ' . count($imagePaths));
            }

            return response()->json([
                'success' => true,
                'message' => 'Property submitted successfully',
                'property_id' => $property->id,
                'images' => $imagePaths,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Property submission error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit property',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
