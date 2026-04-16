<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateFlashcardsRequest;
use App\Services\FlashcardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FlashcardController extends Controller
{
    public function __construct(
        private FlashcardService $flashcardService
    ) {}

    /**
     * Generate flashcards from lecture notes or topic
     */
    public function generate(GenerateFlashcardsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $flashcards = $this->flashcardService->generateFlashcards(
            $validated['content'],
            $validated['type']
        );

        // Log the generation
        $this->flashcardService->logUsage(
            Auth::user(),
            'generate',
            [
                'type' => $validated['type'],
                'content_length' => strlen($validated['content']),
                'flashcards_generated' => count($flashcards),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $flashcards,
        ]);
    }
}
