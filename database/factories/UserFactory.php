<?php
namespace Database\Factories;

use AgenterLab\IAM\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(20),
            'email' => $this->faker->safeEmail(),
            'country' => $this->faker->countryCode()
        ];
    }
}