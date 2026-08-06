<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private const ACTIVATION_LINK_TTL_HOURS = 72;

    public function getUsers()
    {
        $users = User::with('roles')->withTrashed();
        if(!Auth::user()->hasRole('TI')){
            $users->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'TI');
            });
        }
        $users = $users->get();
        $roles = Role::all();
        $zones = Zone::all();

        return Inertia::render('Users/UsersList', [
            'users' => $users,
            'roles' => $roles,
            'zones' => $zones,
            'boss' => $this->bossCandidates(),
            'hierarchy' => $this->hierarchyPayload(),
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:' . User::class,
            'name' => 'required|string|max:255',
            'role_id' => 'required|integer|exists:roles,id',
            'boss_user' => 'nullable|integer|exists:users,user_id',
            'zone_id' => 'nullable|integer|exists:zones,zone_id',
            'location_id' => 'nullable|integer|exists:locations,location_id',
            'enabled' => 'nullable|boolean',
        ]);

        $this->assertAssignableRoles([$validated['role_id']]);
        $this->assertValidBoss([$validated['role_id']], $validated['boss_user'] ?? null);

        $user = DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'username' => (string) $validated['username'],
                'name' => (string) $validated['name'],
                'password' => Hash::make(Str::password(16, true, true, true, false)),
                'boss_user' => $request->filled('boss_user') ? (int) $request->boss_user : null,
                'enabled' => $this->resolveEnabled($request, [$validated['role_id']]),
                'location_id' => $request->filled('location_id') ? (int) $request->location_id : null,
                'default_password' => true,
                'zone_id' => $request->filled('zone_id') ? (int) $request->zone_id : null,
            ]);

            $user->syncRoles($validated['role_id']);

            return $user;
        });

        event(new Registered($user));

        return redirect()->route('users.list')
            ->with('activation_url', $this->activationUrl($user))
            ->with('success', 'Usuario creado. Entregue el enlace de activación al titular de la cuenta.');
    }

    public function editUser(Request $request, $user_id)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user_id . ',user_id',
            'name' => 'required|string|max:255',
            'boss_user' => 'nullable|integer|exists:users,user_id',
        ]);

        $user = User::findOrFail($user_id);

        $this->assertCanManage($user);

        if ($request->has('boss_user')) {
            $this->assertValidBoss(
                $user->roles->pluck('id')->all(),
                $request->filled('boss_user') ? (int) $request->boss_user : null,
                (int) $user_id
            );
        }

        $user->update($this->optionalUserAttributes($request, $user) + [
            'username' => (string) $request->username,
            'name' => (string) $request->name,
            'enabled' => $this->resolveEnabled($request, $user->roles->pluck('id')->all()),
        ]);

        return redirect()->route('users.list');
    }

    public function destroyUser($user_id)
    {
        $this->assertCanManage(User::findOrFail($user_id));

        try {
            $user = User::findOrFail($user_id);
            $user->delete();
            return redirect()->route('users.list');

        } catch (\Exception $e) {
            return back();
        }
    }

    public function enableUser($user_id)
    {
        $this->assertCanManage(User::withTrashed()->findOrFail($user_id));

        try {
            $user = User::withTrashed()->findOrFail($user_id);
            $user->restore();
            $user->default_password = true;
            $user->save();

            return redirect()->route('users.list')
                ->with('activation_url', $this->activationUrl($user))
                ->with('success', 'Usuario reactivado. Entregue el enlace de activación al titular de la cuenta.');
        } catch (\Exception $e) {
            return back();
        }
    }

    public function detailUser($user_id)
    {
        $user = User::with('roles', 'permissions')->withTrashed()->findOrFail($user_id);
        $currentUser = Auth::user();
        $zones = Zone::all();
        $roles = Role::with('permissions');
        if(!$currentUser->hasRole('TI')){
            $roles = $roles->where('name', '!=', 'TI');
            if($user->hasRole('TI')){
                return redirect()->route('users.list');
            }
        }
        $roles = $roles->get();
        $permissions = Permission::all();

        if ($user) {
            return Inertia::render('Users/UserDetail', [
                'user' => $user,
                'roles' => $roles,
                'zones' => $zones,
                'permissions' => $permissions,
                'boss' => $this->bossCandidates(),
                'hierarchy' => $this->hierarchyPayload(),
            ]);
        } else {
            return redirect()->route('users.list');
        }
    }

    public function updateUserRolePermission(Request $request, $user_id)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'boss_user' => 'nullable|integer|exists:users,user_id',
            'username' => 'required|string|max:255|unique:users,username,' . $user_id . ',user_id',
            'name' => 'required|string|max:255',
        ]);

        $this->assertAssignableRoles($validated['roles']);

        $user = User::findOrFail($user_id);

        $this->assertCanManage($user);
        $this->assertAssignablePermissions($validated['permissions'] ?? []);
        $this->assertValidBoss(
            $validated['roles'],
            $request->filled('boss_user') ? (int) $request->boss_user : null,
            (int) $user_id
        );

        $enabled = $this->resolveEnabled($request, $validated['roles']);

        DB::transaction(function () use ($request, $validated, $user, $enabled) {
            if (! $enabled) {
                $user->location_user()->detach();
            }

            $user->update($this->optionalUserAttributes($request, $user) + [
                'username' => (string) $validated['username'],
                'name' => (string) $validated['name'],
                'enabled' => $enabled,
            ]);

            $user->syncRoles($validated['roles']);
            $user->syncPermissions($validated['permissions'] ?? []);
        });

        return redirect()->route('users.list');
    }

    public function resetPassword(Request $request, $user_id)
    {
        $request->validate([
            'default_password' => 'boolean',
        ]);

        $user = User::findOrFail($user_id);

        $this->assertCanManage($user);

        $user->update([
            'default_password' => true,
            'password' => Hash::make(Str::password(16, true, true, true, false)),
        ]);

        return redirect()->route('users.detail', $user_id)
            ->with('activation_url', $this->activationUrl($user))
            ->with('success', 'Contraseña restablecida. Entregue el enlace de activación al titular de la cuenta.');
    }

    public function getPermissionRol(Request $request, $roles_id='')
    {
        $permissions = [];
        $result = [];
        foreach (explode(',', $roles_id) as $role_id) {
            $role = Role::findOrFail($role_id);
            if($role->permissions->all()){
                $result = array_merge($role->permissions->all(), $permissions);
            }
        }
        return $result;
    }

    public function getPermissions()
    {
        $permissions = Permission::all();
        return Inertia::render('Users/Permissions', ['permissions' => $permissions]);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $permission = Permission::make(['guard_name' => 'web','name' => $request->name]);
        $permission->saveOrFail();

        Role::whereIn('name', ['Administrador', 'TI'])
            ->get()
            ->each(fn (Role $role) => $role->givePermissionTo($permission));

        return redirect('permissions');
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $permission = Permission::findOrFail($request->id);
        $permission->update(['guard_name' => 'web','name' => $request->name]);
        return redirect('permissions');
    }

    public function getRoles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return Inertia::render('Users/Roles', ['roles' => $roles, 'permissions' => $permissions]);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'required',
        ]);
        $role = Role::make(['guard_name' => 'web','name' => $request->name]); 
        $role->saveOrFail();
        $role->syncPermissions($request->permissions);
        return redirect('roles');
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $role = Role::findOrFail($request->id);
        $role->update(['guard_name' => 'web','name' => $request->name]);
        $role->syncPermissions($request->permissions);
        return redirect('roles');
    }

    /**
     * `enabled` es la bandera de cuenta activa: la comprueban el login
     * (AuthenticatedSessionController), el middleware de inventario
     * (CheckInventoryAccess) y el enlace de activación
     * (PasswordChangeController). Pero el formulario solo la pregunta —como
     * «¿utilizará la caja?»— a los roles que operan caja.
     *
     * Sin esta distinción, toda cuenta creada con cualquier otro rol nacía con
     * `enabled = 0` y su enlace de activación era rechazado con «La cuenta no
     * admite el cambio de contraseña por esta vía».
     */
    private function resolveEnabled(Request $request, array $roleIds): bool
    {
        $cashRoles = config('prais.hierarchy.cash_register_roles', []);

        $usesCashRegister = Role::whereIn('id', $roleIds)
            ->whereIn('name', $cashRoles)
            ->exists();

        return $usesCashRegister ? $request->boolean('enabled') : true;
    }

    /**
     * Usuarios que pueden figurar como jefe: los que tienen alguno de los roles
     * que la jerarquía declara como superiores. Se excluyen las cuentas
     * eliminadas: colgar a alguien de un jefe dado de baja deja la línea rota.
     */
    private function bossCandidates()
    {
        $bossRoles = array_values(array_unique(config('prais.hierarchy.boss_role', [])));

        return User::with(['roles', 'zone'])
            ->whereHas('roles', fn ($query) => $query->whereIn('name', $bossRoles))
            ->get(['user_id', 'name', 'zone_id']);
    }

    /**
     * Traduce la jerarquía declarada por nombre de rol a los ids que maneja el
     * formulario, para que la vista no vuelva a llevar ids quemados.
     */
    private function hierarchyPayload(): array
    {
        $roleIds = Role::pluck('id', 'name');

        $bossRole = [];
        foreach (config('prais.hierarchy.boss_role', []) as $roleName => $bossRoleName) {
            if (isset($roleIds[$roleName], $roleIds[$bossRoleName])) {
                $bossRole[$roleIds[$roleName]] = [
                    'role_id' => $roleIds[$bossRoleName],
                    'role_name' => $bossRoleName,
                ];
            }
        }

        $toIds = fn (array $names) => collect($names)
            ->map(fn ($name) => $roleIds[$name] ?? null)
            ->filter()
            ->values();

        return [
            'bossRole' => $bossRole,
            'cashRegisterRoles' => $toIds(config('prais.hierarchy.cash_register_roles', [])),
            'zoneRoles' => $toIds(config('prais.hierarchy.zone_roles', [])),
        ];
    }

    /**
     * Rol que debe tener el jefe de un usuario con los roles indicados, o null
     * si ninguno de esos roles depende de otra cuenta.
     */
    private function requiredBossRole(array $roleIds): ?string
    {
        $map = config('prais.hierarchy.boss_role', []);

        foreach (Role::whereIn('id', $roleIds)->pluck('name') as $name) {
            if (isset($map[$name])) {
                return $map[$name];
            }
        }

        return null;
    }

    private function assertValidBoss(array $roleIds, ?int $bossId, ?int $targetUserId = null): void
    {
        $bossRole = $this->requiredBossRole($roleIds);

        if ($bossRole === null) {
            return;
        }

        if ($bossId === null) {
            throw ValidationException::withMessages([
                'boss_user' => "Este rol depende de un jefe con rol {$bossRole}. Cree primero esa cuenta.",
            ]);
        }

        if ($targetUserId !== null && $bossId === $targetUserId) {
            throw ValidationException::withMessages([
                'boss_user' => 'Un usuario no puede ser su propio jefe.',
            ]);
        }

        if (! User::find($bossId)?->hasRole($bossRole)) {
            throw ValidationException::withMessages([
                'boss_user' => "El jefe asignado debe ser un usuario activo con rol {$bossRole}.",
            ]);
        }
    }

    private function activationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'password.change',
            now()->addHours(self::ACTIVATION_LINK_TTL_HOURS),
            ['username' => $user->username]
        );
    }

    private function assertAssignableRoles(array $roleIds): void
    {
        if (Auth::user()?->hasRole('TI')) {
            return;
        }

        $restricted = Role::whereIn('id', $roleIds)->where('name', 'TI')->exists();

        if ($restricted) {
            throw ValidationException::withMessages([
                'roles' => 'No tiene autorización para asignar el rol TI.',
            ]);
        }
    }

    private function assertCanManage(User $target): void
    {
        if (Auth::user()?->hasRole('TI')) {
            return;
        }

        if ($target->hasRole('TI')) {
            abort(403, 'No tiene autorización para administrar una cuenta TI.');
        }
    }

    private function assertAssignablePermissions(array $permissionIds): void
    {
        if (Auth::user()?->hasRole('TI') || empty($permissionIds)) {
            return;
        }

        $requested = Permission::whereIn('id', $permissionIds)->pluck('name');
        $own = Auth::user()->getAllPermissions()->pluck('name');

        if ($requested->diff($own)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'permissions' => 'No tiene autorización para asignar permisos que usted no posee.',
            ]);
        }
    }

    private function optionalUserAttributes(Request $request, User $user): array
    {
        $attributes = [];

        if ($request->has('boss_user')) {
            $attributes['boss_user'] = $request->filled('boss_user') ? (int) $request->boss_user : null;
        }

        if ($request->has('zone_id')) {
            $attributes['zone_id'] = $request->filled('zone_id') ? (int) $request->zone_id : null;
        }

        // `enabled` no se toma en crudo: lo resuelve resolveEnabled() en cada
        // acción, que sabe si el rol responde la pregunta de la caja.

        if ($request->has('default_password')) {
            $attributes['default_password'] = $request->boolean('default_password');
        }

        return $attributes;
    }
}