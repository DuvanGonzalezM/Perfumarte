<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use RuntimeException;
use Spatie\Permission\Models\Role;

/**
 * Crea la cuenta técnica inicial (rol TI) sobre una base de datos nueva.
 *
 * Lo usan tanto `php artisan prais:bootstrap` como `AdminUserSeeder`, de modo
 * que ambos caminos apliquen exactamente las mismas reglas de seguridad.
 */
class InitialAdminProvisioner
{
    public const ROLE = 'TI';

    /**
     * ¿Existe ya alguna cuenta con rol TI? El arranque es de una sola vez.
     */
    public function alreadyProvisioned(): bool
    {
        return User::withTrashed()->role(self::ROLE)->exists();
    }

    public function usernameTaken(string $username): bool
    {
        return User::withTrashed()->where('username', $username)->exists();
    }

    /**
     * El rol TI lo crea RolePermissionSeeder; sin él no se puede asignar.
     */
    public function roleExists(): bool
    {
        return Role::where('name', self::ROLE)->where('guard_name', 'web')->exists();
    }

    /**
     * Reglas de la contraseña de la cuenta TI inicial: más estrictas que las
     * del resto de la aplicación porque es una cuenta con todos los permisos.
     *
     * `uncompromised()` consulta la lista de contraseñas filtradas (k-anonymity,
     * nunca envía la contraseña); si no hay red, la regla no bloquea.
     *
     * @return array<int, string> Lista de errores; vacía si la contraseña es válida.
     */
    public function passwordErrors(string $password, string $username): array
    {
        $rule = Password::min(config('prais.bootstrap.min_password_length'))
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();

        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', $rule]]
        );

        $validator->after(function ($validator) use ($password, $username) {
            if ($username !== '' && Str::contains(Str::lower($password), Str::lower($username))) {
                $validator->errors()->add('password', 'La contraseña no puede contener el nombre de usuario.');
            }
        });

        return $validator->errors()->get('password');
    }

    /**
     * Crea la cuenta TI.
     *
     * Si no se entrega contraseña se genera una aleatoria que nadie ve y se
     * devuelve un enlace de activación firmado con vencimiento, para que el
     * titular fije la suya. Cuando se fuerza el cambio, la cuenta queda con
     * `default_password = true` y `CheckFirstLogin` no la deja entrar a ningún
     * módulo hasta que la cambie.
     *
     * @return array{user: User, activation_url: string|null}
     */
    public function provision(string $username, string $name, ?string $plainPassword = null, bool $forceChange = true): array
    {
        if (! $this->roleExists()) {
            throw new RuntimeException(
                'No existe el rol "'.self::ROLE.'". Ejecute primero: php artisan db:seed --class=Database\\Seeders\\RolePermissionSeeder'
            );
        }

        if ($this->usernameTaken($username)) {
            throw new RuntimeException("Ya existe un usuario con el nombre '{$username}'.");
        }

        // Sin contraseña entregada, la cuenta nace inutilizable hasta que el
        // titular la active con el enlace firmado.
        if ($plainPassword === null) {
            $plainPassword = Str::password(32, true, true, true, false);
            $forceChange = true;
        }

        $user = DB::transaction(function () use ($username, $name, $plainPassword, $forceChange) {
            $user = User::create([
                'username' => $username,
                'name' => $name,
                'password' => Hash::make($plainPassword),
                'enabled' => true,
                'default_password' => $forceChange,
            ]);

            $user->assignRole(self::ROLE);

            return $user;
        });

        return [
            'user' => $user,
            'activation_url' => $forceChange ? $this->activationUrl($user) : null,
        ];
    }

    /**
     * Enlace firmado y con vencimiento hacia el formulario de activación.
     */
    public function activationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'password.change',
            now()->addHours(config('prais.bootstrap.activation_ttl_hours')),
            ['username' => $user->username]
        );
    }
}
