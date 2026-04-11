<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlashcardSetRequest;
use App\Http\Requests\UpdateFlashcardSetRequest;
use App\Models\FlashcardSet;
use App\Services\FlashcardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FlashcardSetController extends Controller
{
    public function __construct(
        private FlashcardService $flashcardService
    ) {}

    /**
     * List all flashcard sets for the authenticated user
     */
    public function index(): JsonResponse
    {
        $sets = FlashcardSet::with(['flashcards', 'tags'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sets,
        ]);
    }

    /**
     * Store a new flashcard set
     */
    public function store(StoreFlashcardSetRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $set = $this->flashcardService->saveFlashcardSet(
            Auth::user(),
            $validated['title'],
            $validated['flashcards'],
            $validated['tag_ids'] ?? []
        );

        return response()->json([
            'success' => true,
            'data' => $set,
        ], 201);
    }

    /**
     * Show a specific flashcard set
     */
    public function show(FlashcardSet $set): JsonResponse
    {
        // Check authorization
        if ($set->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $set->load(['flashcards', 'tags']);

        $this->flashcardService->logUsage(
            Auth::user(),
            'view_set',
            ['set_id' => $set->id]
        );

        return response()->json([
            'success' => true,
            'data' => $set,
        ]);
    }

    /**
     * Update a flashcard set
     */
    public function update(UpdateFlashcardSetRequest $request, FlashcardSet $set): JsonResponse
    {
        // Check authorization
        if ($set->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validated();

        $set = $this->flashcardService->updateFlashcardSet(
            $set,
            $validated['title'],
            $validated['flashcards'],
            $validated['tag_ids'] ?? []
        );

        return response()->json([
            'success' => true,
            'data' => $set,
        ]);
    }

    /**
     * Delete a flashcard set
     */
    public function destroy(FlashcardSet $set): JsonResponse
    {
        // Check authorization
        if ($set->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $set->delete();

        return response()->json([
            'success' => true,
            'message' => 'Flashcard set deleted successfully',
        ]);
    }
}
