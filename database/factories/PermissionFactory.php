<?php
namespace Database\Factories;

use AgenterLab\IAM\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
    
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->realText(20);

        return [
            'title' => $name,
            'name' => Str::slug($name)
        ];
    }

    /**
     * Indicate that the model has permission.
     * 
     * @param string $permission
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function permission(string $permission)
    {
        return $this->state([
            'title' => $permission,
            'name' => Str::slug($permission)
        ]);
    }
}