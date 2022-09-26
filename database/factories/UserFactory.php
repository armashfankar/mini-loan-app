<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Helpers\UtilHelper as Util;

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
            'user_reference_number' => $this->generateReferenceNumber(),
            'name' => "Alex Dan",
            'email' => "alex@miniloan.com",
            'password' => '$2y$10$KeHthg2WhwIgJWGtBdhtq.xF6L9Y0PaVh.kXfuJIw5xxW02aTawKS', // asdasdasd
        ];
    }

    /**
     * @return string
     */
    private function generateReferenceNumber()
    {
        return 'USR' . Util::generateString(true);
    }
    
}
