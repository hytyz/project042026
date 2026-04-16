<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * List all tags for the authenticated user
     */
    public function index(): JsonResponse
    {
        $tags = Tag::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tags,
        ]);
    }

    /**
     * Store a new tag
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
        ]);

        $tag = Tag::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $tag,
        ], 201);
    }

    /**
     * Update a tag
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        if ($tag->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($tag->id),
            ],
        ]);

        $tag->update(['name' => $validated['name']]);

        return response()->json([
            'success' => true,
            'data' => $tag,
        ]);
    }

    /**
     * Delete a tag
     */
    public function destroy(Tag $tag): JsonResponse
    {
        if ($tag->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully',
        ]);
    }
}
