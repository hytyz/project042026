<?php

namespace App\Http\Controllers;

use App\Models\FlashcardSet;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SetController extends Controller
{
    /**
     * Show the edit page for a flashcard set
     */
    public function show(FlashcardSet $set): Response
    {
        // Check authorization
        if ($set->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('sets/show', [
            'id' => (string) $set->id,
        ]);
    }

    /**
     * Show the practice page for a flashcard set
     */
    public function practice(FlashcardSet $set): Response
    {
        if ($set->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('sets/practice', [
            'id' => (string) $set->id,
        ]);
    }
}
