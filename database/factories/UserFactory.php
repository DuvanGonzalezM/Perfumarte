<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 *
 * La versión anterior era la de Laravel Breeze sin adaptar: generaba `email`,
 * `email_verified_at` y `remember_token`, columnas que la tabla `users` de este
 * proyecto nunca ha tenido. Por eso 23 de las 25 pruebas existentes fallaban
 * con "Unknown column 'email'" desde el primer día.
 */
class UserFactory extends Factory
{
    /**
     * Contraseña compartida por las instancias generadas.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'name' => fake()->name(),
            'password' => static::$password ??= Hash::make('password'),
            'enabled' => true,
            'default_password' => false,
        ];
    }

    /**
     * Cuenta pendiente de activación: su contraseña es la aleatoria que genera
     * el administrador y solo puede fijarse mediante enlace firmado.
     */
    public function withDefaultPassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'default_password' => true,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }
}
