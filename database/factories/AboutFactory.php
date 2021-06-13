<?php

namespace Database\Factories;

use App\Models\About;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class AboutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = About::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'photo' => (UploadedFile::fake()->image('image.jpg'))->getClientOriginalName(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'description' => $this->faker->sentence(),
            'cv' => (UploadedFile::fake()->create(
                        sprintf("%s.pdf", $this->faker->word()), 
                        1024, 
                        'application/pdf'
                    ))->getClientOriginalName()
        ];
    }
}
