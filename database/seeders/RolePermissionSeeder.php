<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'Dashboard' => [
            'Ver Dashboard',
        ],
        'Inventario' => [
            'Ver Inventario Sede',
            'Ver Stock',
            'Editar Stock',
        ],
        'Ordenes de compra' => [
            'Ver Ordenes de Compra',
            'Crear Ordenes de Compra',
            'Editar Ordenes de Compra',
        ],
        'Despachos' => [
            'Ver Despachos',
            'Crear Despachos',
            'Editar Despachos',
            'Aprobar Despachos',
            'Recibir Insumos',
        ],
        'Solicitudes' => [
            'Ver Solicitudes Insumos',
            'Crear Solicitudes Insumos',
            'Editar Solicitudes Insumos',
            'Ver Solicitudes Transformacion',
            'Crear Solicitudes Transformacion',
        ],
        'Laboratorio' => [
            'Ver Reenvases',
            'Crear Reenvases',
            'Editar Reenvases',
            'Ver Transformaciones',
            'Crear Transformaciones',
            'Editar Transformaciones',
        ],
        'Ventas y caja' => [
            'Ver Ventas',
            'Crear Ventas',
            'Cerrar Caja',
        ],
        'Sedes y personal' => [
            'Ver Sedes',
            'Crear Sedes',
            'Editar Sedes',
            'Eliminar Sedes',
            'Asignar Supervisor',
            'Asignar Personal',
        ],
        'Catálogo' => [
            'Ver Productos',
            'Crear Productos',
            'Editar Productos',
            'Desactivar Productos',
            'Ver Proveedores',
            'Crear Proveedores',
            'Editar Proveedores',
            'Desactivar Proveedores',
        ],
        'Devoluciones y consumibles' => [
            'Ver Devoluciones',
            'Crear Devoluciones',
            'Confirmar Devoluciones',
            'Aprobar Devoluciones',
            'Ver Consumibles',
            'Crear Consumibles',
        ],
        'Auditoría' => [
            'Auditar',
        ],
        'Novedades' => [
            'Ver Novedades',
            'Crear Novedades',
        ],
        'Reportes' => [
            'Ver Reportes',
            'Crear Reporte',
        ],
        'Administración' => [
            'Ver Usuarios',
            'Editar Usuarios',
            'Crear Roles',
            'Crear Permisos',
        ],
    ];

    private const TI_ONLY = [
        'Crear Roles',
        'Crear Permisos',
    ];

    private const ROLE_MATRIX = [
        'TI' => '*',

        'Administrador' => '*',

        'Gerencia' => [
            'Ver Dashboard', 'Ver Stock', 'Ver Inventario Sede',
            'Ver Ordenes de Compra', 'Crear Ordenes de Compra', 'Editar Ordenes de Compra',
            'Ver Despachos', 'Aprobar Despachos',
            'Ver Solicitudes Insumos', 'Ver Solicitudes Transformacion',
            'Ver Reenvases', 'Ver Transformaciones',
            'Ver Ventas', 'Ver Sedes', 'Asignar Supervisor', 'Asignar Personal',
            'Ver Productos', 'Ver Proveedores',
            'Ver Devoluciones', 'Aprobar Devoluciones', 'Ver Consumibles',
            'Auditar', 'Ver Novedades', 'Ver Reportes', 'Crear Reporte',
            'Ver Usuarios',
        ],

        'Subdirector' => [
            'Ver Dashboard', 'Ver Stock', 'Ver Inventario Sede',
            'Ver Despachos', 'Ver Solicitudes Insumos', 'Ver Solicitudes Transformacion',
            'Ver Ventas', 'Ver Sedes', 'Asignar Personal',
            'Ver Productos', 'Ver Devoluciones', 'Ver Consumibles',
            'Auditar', 'Ver Novedades', 'Crear Novedades', 'Ver Reportes', 'Crear Reporte',
        ],

        'Supervisor' => [
            'Ver Dashboard', 'Ver Stock', 'Ver Inventario Sede',
            'Ver Despachos', 'Ver Solicitudes Insumos', 'Ver Ventas', 'Ver Sedes',
            'Ver Devoluciones', 'Confirmar Devoluciones', 'Ver Consumibles',
            'Auditar', 'Ver Novedades', 'Crear Novedades',
        ],

        'Jefe de operaciones' => [
            'Ver Dashboard', 'Ver Stock', 'Editar Stock', 'Ver Inventario Sede',
            'Ver Despachos', 'Crear Despachos', 'Editar Despachos', 'Aprobar Despachos',
            'Recibir Insumos',
            'Ver Solicitudes Insumos', 'Editar Solicitudes Insumos',
            'Ver Solicitudes Transformacion',
            'Ver Sedes', 'Asignar Personal',
            'Ver Productos', 'Ver Proveedores',
            'Ver Devoluciones', 'Confirmar Devoluciones', 'Aprobar Devoluciones',
            'Ver Consumibles', 'Ver Novedades', 'Crear Novedades',
        ],

        'Auxiliar administrativo' => [
            'Ver Dashboard', 'Ver Stock',
            'Ver Ordenes de Compra', 'Crear Ordenes de Compra', 'Editar Ordenes de Compra',
            'Ver Despachos', 'Ver Solicitudes Insumos',
            'Ver Productos', 'Crear Productos', 'Editar Productos', 'Desactivar Productos',
            'Ver Proveedores', 'Crear Proveedores', 'Editar Proveedores', 'Desactivar Proveedores',
            'Ver Consumibles', 'Crear Consumibles',
            'Ver Novedades', 'Crear Novedades',
        ],

        'Laboratorio' => [
            'Ver Dashboard', 'Ver Stock',
            'Ver Reenvases', 'Crear Reenvases', 'Editar Reenvases',
            'Ver Transformaciones', 'Crear Transformaciones', 'Editar Transformaciones',
            'Ver Solicitudes Transformacion',
            'Ver Consumibles', 'Crear Consumibles',
            'Ver Novedades', 'Crear Novedades',
        ],

        'Asesor comercial' => [
            'Ver Inventario Sede',
            'Ver Ventas', 'Crear Ventas', 'Cerrar Caja',
            'Ver Solicitudes Insumos', 'Crear Solicitudes Insumos',
            'Ver Solicitudes Transformacion', 'Crear Solicitudes Transformacion',
            'Recibir Insumos',
            'Ver Devoluciones', 'Crear Devoluciones',
            'Ver Consumibles', 'Crear Consumibles',
            'Ver Novedades', 'Crear Novedades',
        ],

        'Control gerencia' => [
            'Ver Dashboard', 'Ver Stock', 'Ver Despachos', 'Ver Ventas',
            'Ver Sedes', 'Ver Reportes',
        ],

        'Monitoreo' => [
            'Ver Dashboard', 'Ver Stock', 'Ver Ventas', 'Ver Reportes',
        ],

        'Usuario' => [
            'Ver Inventario Sede', 'Ver Ventas',
        ],
    ];

    public function run(): void
    {
        App::make(PermissionRegistrar::class)->forgetCachedPermissions();

        $all = [];

        foreach (self::PERMISSIONS as $permissions) {
            foreach ($permissions as $name) {
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $all[] = $name;
            }
        }

        foreach (self::ROLE_MATRIX as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            $granted = $permissions === '*' ? $all : $permissions;

            if ($roleName !== 'TI') {
                $granted = array_values(array_diff($granted, self::TI_ONLY));
            }

            $role->givePermissionTo($granted);
        }

        App::make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info(sprintf(
            'Roles y permisos: %d permisos y %d roles.',
            count($all),
            count(self::ROLE_MATRIX)
        ));
    }
}
