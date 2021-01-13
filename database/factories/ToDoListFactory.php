<?php

namespace Database\Factories;

use App\Models\ToDoList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToDoListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ToDoList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->lazy(),
            'title' => $this->faker->company,
            'description' => $this->faker->text(),
            'deadline_at' => $this->faker->dateTimeBetween('+3 days', '+10 years'),
        ];
    }
}
