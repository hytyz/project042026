<?php

namespace Database\Factories;

use App\Models\Flashcard;
use App\Models\FlashcardSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Flashcard>
 */
class FlashcardFactory extends Factory
{
    protected $model = Flashcard::class;

    public function definition(): array
    {
        return [
            'set_id' => FlashcardSet::factory(),
            'question' => $this->faker->sentence().'?',
            'answer' => $this->faker->paragraph(),
        ];
    }
}
