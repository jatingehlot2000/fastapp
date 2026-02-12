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
                $image->media_url = 'https://test.teerthsewanyas.org/' . $image->image_path;
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
                'images' => 'nullable|array|max:3',
                'videos.*' => 'nullable|mimes:mp4,mov,avi,wmv|max:256000',
                'videos' => 'nullable|array|max:3',
            ]);

            if ($validator->fails()) {
                Log::error('Property validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $propertyData = $request->except(['images', 'videos']);
            $propertyData['user_id'] = $request->user()->id;

            $property = Property::create($propertyData);
            Log::info('Property created with ID: ' . $property->id);

            $mediaPaths = [];

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
                        'media_type' => 'image',
                    ]);
                    $mediaPaths[] = 'https://test.teerthsewanyas.org/' . $imagePath;
                }
            }

            if ($request->hasFile('videos')) {
                $uploadPath = public_path('property_videos');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($request->file('videos') as $video) {
                    $filename = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                    $video->move($uploadPath, $filename);
                    $videoPath = 'property_videos/' . $filename;

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $videoPath,
                        'media_type' => 'video',
                    ]);
                    $mediaPaths[] = 'https://test.teerthsewanyas.org/' . $videoPath;
                }
            }

            Log::info('Media uploaded: ' . count($mediaPaths));

            return response()->json([
                'success' => true,
                'message' => 'Property submitted successfully',
                'property_id' => $property->id,
                'media' => $mediaPaths,
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

    public function update(Request $request, $id)
    {
        try {
            Log::info('Property update request:', $request->all());

            $property = Property::find($id);
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            if ($property->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this property'
                ], 403);
            }

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
                'images' => 'nullable|array|max:3',
                'videos.*' => 'nullable|mimes:mp4,mov,avi,wmv|max:256000',
                'videos' => 'nullable|array|max:3',
            ]);

            if ($validator->fails()) {
                Log::error('Property validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $propertyData = $request->except(['images', 'videos']);
            $property->update($propertyData);
            Log::info('Property updated with ID: ' . $property->id);

            $mediaPaths = [];

            if ($request->hasFile('images')) {
                // Delete existing images
                $existingImages = PropertyImage::where('property_id', $property->id)->where('media_type', 'image')->get();
                foreach ($existingImages as $image) {
                    $filePath = public_path($image->image_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $image->delete();
                }

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
                        'media_type' => 'image',
                    ]);
                    $mediaPaths[] = 'https://test.teerthsewanyas.org/' . $imagePath;
                }
            }

            if ($request->hasFile('videos')) {
                // Delete existing videos
                $existingVideos = PropertyImage::where('property_id', $property->id)->where('media_type', 'video')->get();
                foreach ($existingVideos as $video) {
                    $filePath = public_path($video->image_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $video->delete();
                }

                $uploadPath = public_path('property_videos');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($request->file('videos') as $video) {
                    $filename = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                    $video->move($uploadPath, $filename);
                    $videoPath = 'property_videos/' . $filename;

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $videoPath,
                        'media_type' => 'video',
                    ]);
                    $mediaPaths[] = 'https://test.teerthsewanyas.org/' . $videoPath;
                }
            }

            Log::info('Media uploaded: ' . count($mediaPaths));

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'property_id' => $property->id,
                'media' => $mediaPaths,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Property update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $property = Property::find($id);
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            if ($property->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this property'
                ], 403);
            }

            $images = PropertyImage::where('property_id', $id)->get();
            foreach ($images as $image) {
                $filePath = public_path($image->image_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $image->delete();
            }

            $property->delete();
            Log::info('Property deleted with ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Property delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
