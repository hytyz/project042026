<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\UsageLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlashcardService
{
    /**
     * Generate flashcards using AI (OpenRouter) or simulation for testing
     *
     * @param  string  $content  The lecture notes or topic
     * @param  string  $type  Either 'notes' or 'topic'
     * @return array Array of flashcards with question and answer
     */
    public function generateFlashcards(string $content, string $type = 'notes'): array
    {
        $apiKey = config('services.openrouter.api_key');

        if (! empty($apiKey)) {
            try {
                return $this->generateWithAI($content, $type);
            } catch (\Exception $e) {
                Log::error('AI generation failed, falling back to simulated api: '.$e->getMessage());

                return $this->simulateGeneration($content, $type);
            }
        }

        return $this->simulateGeneration($content, $type);
    }

    /**
     * Generate flashcards using OpenRouter API
     */
    private function generateWithAI(string $content, string $type): array
    {
        $prompt = $type === 'topic'
            ? "Generate 10 educational flashcards about the topic: \"{$content}\". Each flashcard should have a question and a concise answer. Return ONLY a JSON array in this exact format: [{\"question\": \"...\", \"answer\": \"...\"}, ...]"
            : "Generate 10 educational flashcards from these lecture notes. Each flashcard should have a question and a concise answer. Return ONLY a JSON array in this exact format: [{\"question\": \"...\", \"answer\": \"...\"}, ...]\n\nLecture Notes:\n{$content}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openrouter.api_key'),
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => 'Flashcard Generator',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => config('services.openrouter.model', 'deepseek/deepseek-v3.2'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful educational assistant that creates high-quality flashcards for studying. Always return valid JSON.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        if (! $response->successful()) {
            throw new \Exception('OpenRouter API error: '.$response->body());
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? '';

        // Extract JSON from response (handle markdown code blocks)
        $content = $this->extractJsonFromResponse($content);

        $flashcards = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($flashcards)) {
            throw new \Exception('Failed to parse AI response as JSON');
        }

        return $flashcards;
    }

    /**
     * Extract JSON from AI response (handles markdown code blocks)
     */
    private function extractJsonFromResponse(string $content): string
    {
        // Try to extract JSON from markdown code blocks
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            return trim($matches[1]);
        }

        // Try to find JSON array
        if (preg_match('/\[[\s\S]*\]/', $content, $matches)) {
            return $matches[0];
        }

        return $content;
    }

    /**
     * Simulate flashcard generation (fallback when no API key)
     */
    private function simulateGeneration(string $content, string $type): array
    {
        if ($type === 'topic') {
            return $this->generateTopicFlashcards($content);
        }

        return $this->generateNotesFlashcards($content);
    }

    /**
     * Generate flashcards for a topic
     */
    private function generateTopicFlashcards(string $topic): array
    {
        $templates = [
            [
                'question' => "What is the basic definition of {$topic}?",
                'answer' => "{$topic} refers to a fundamental concept or subject area that encompasses various theories, principles, and applications.",
            ],
            [
                'question' => "What are the key components of {$topic}?",
                'answer' => 'The key components include: (1) Core principles, (2) Practical applications, (3) Theoretical frameworks, and (4) Historical development.',
            ],
            [
                'question' => "Why is understanding {$topic} important?",
                'answer' => "Understanding {$topic} is crucial because it provides foundational knowledge that enables better decision-making, problem-solving, and professional competence in related fields.",
            ],
            [
                'question' => "What are common challenges when studying {$topic}?",
                'answer' => 'Common challenges include: grasping abstract concepts, connecting theory to practice, keeping up with evolving developments, and applying knowledge to real-world scenarios.',
            ],
            [
                'question' => "How can one best learn about {$topic}?",
                'answer' => 'Effective learning strategies include: studying foundational texts, practicing with real examples, discussing with peers, seeking mentorship, and applying concepts through projects.',
            ],
        ];

        return $templates;
    }

    /**
     * Generate flashcards from lecture notes (simple parsing)
     */
    private function generateNotesFlashcards(string $notes): array
    {
        $flashcards = [];

        // Split by lines and look for key patterns
        $lines = explode("\n", $notes);
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Look for lines that look like definitions or key points
            if (preg_match('/^(.*?)[=:–-](.*)$/u', $line, $matches)) {
                $question = trim($matches[1]).'?';
                $answer = trim($matches[2]);

                // Clean up the question
                $question = ucfirst(preg_replace('/[:=]/', '', $question));
                if (! str_ends_with($question, '?')) {
                    $question .= '?';
                }

                $flashcards[] = [
                    'question' => $question,
                    'answer' => $answer,
                ];
                $count++;
            }

            // Limit to reasonable number
            if ($count >= 8) {
                break;
            }
        }

        // If no structured content found, create generic cards
        if (empty($flashcards)) {
            $flashcards = [
                [
                    'question' => 'What is the main topic of these notes?',
                    'answer' => substr($notes, 0, 200).(strlen($notes) > 200 ? '...' : ''),
                ],
                [
                    'question' => 'What are the key points to remember?',
                    'answer' => 'The notes cover important concepts that should be reviewed regularly for better understanding and retention.',
                ],
                [
                    'question' => 'How should you study this material?',
                    'answer' => 'Review the notes multiple times, create your own questions, and test yourself regularly to reinforce learning.',
                ],
            ];
        }

        return $flashcards;
    }

    /**
     * Log usage action
     */
    public function logUsage(?User $user, string $action, array $metadata = []): void
    {
        UsageLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Save a flashcard set with flashcards
     */
    public function saveFlashcardSet(User $user, string $title, array $flashcards, array $tagIds = []): FlashcardSet
    {
        $set = FlashcardSet::create([
            'user_id' => $user->id,
            'title' => $title,
        ]);

        foreach ($flashcards as $card) {
            Flashcard::create([
                'set_id' => $set->id,
                'question' => $card['question'],
                'answer' => $card['answer'],
            ]);
        }

        if (! empty($tagIds)) {
            $set->tags()->attach($tagIds);
        }

        $this->logUsage($user, 'save_set', [
            'set_id' => $set->id,
            'flashcard_count' => count($flashcards),
        ]);

        return $set->load('flashcards', 'tags');
    }

    /**
     * Update flashcard set
     */
    public function updateFlashcardSet(FlashcardSet $set, string $title, array $flashcards, array $tagIds = []): FlashcardSet
    {
        $set->update(['title' => $title]);

        // Delete existing flashcards and recreate
        $set->flashcards()->delete();

        foreach ($flashcards as $card) {
            Flashcard::create([
                'set_id' => $set->id,
                'question' => $card['question'],
                'answer' => $card['answer'],
            ]);
        }

        $set->tags()->sync($tagIds);

        return $set->load('flashcards', 'tags');
    }
}
