<?php

namespace Database\Factories;

use App\Models\FlashcardSet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashcardSet>
 */
class FlashcardSetFactory extends Factory
{
    protected $model = FlashcardSet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
        ];
    }
}
