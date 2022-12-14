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
            'name' => "John Doe",
            'email' => "admin@miniloan.com",
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
}
