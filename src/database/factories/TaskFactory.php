<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Status;
use DB;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = Status::cases();
        $randomStatus = $statuses[array_rand($statuses)];

        return [
            'user_id' => $this->getRandomTableId('users'),
            'title' => fake()->text('250'),
            'description' => fake()->text('2000'),
            'status' => $randomStatus->value,
            'priority' => fake()->numberBetween(1, 5),
            'completed_at' => ($randomStatus->value == Status::DONE->value) ? fake()->dateTime : null,
            'parent_id' => $this->getRandomTableId('tasks'),
            'created_at' => fake()->dateTime,
            'updated_at' => fake()->dateTime,
        ];
    }

    protected function getRandomTableId(string $table): null|int
    {
        return DB::table($table)->inRandomOrder()->limit(1)->value('id');
    }
}
