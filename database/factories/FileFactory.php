<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'hash_name' => $this->faker->word,
            'path' => $this->faker->word,
            'mime_type' => $this->faker->mimeType,
            'size' => $this->faker->randomNumber(),
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
        ];
    }
}
