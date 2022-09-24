<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Helpers\UtilHelper as Util;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_reference_number' => $this->generateReferenceNumber(),
            'name' => "Thanh Nghiem",
            'email' => "admin@aspireapp.com",
            'password' => '$2y$10$KeHthg2WhwIgJWGtBdhtq.xF6L9Y0PaVh.kXfuJIw5xxW02aTawKS', // asdasdasd
        ];
    }

    /**
     * @return string
     */
    private function generateReferenceNumber()
    {
        return 'ADM' . Util::generateString(true);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
