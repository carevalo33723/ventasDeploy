<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [

            //Registro de actividad
            'ver-registro-actividad',

            //Cajas
            'ver-caja',
            'ver-todas-cajas', // 🔥 NUEVO
            'aperturar-caja',
            'cerrar-caja',

            //Kardex
            'ver-kardex',

            //categorías
            'ver-categoria',
            'crear-categoria',
            'editar-categoria',
            'eliminar-categoria',

            //Cliente
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'eliminar-cliente',

            //Compra
            'ver-compra',
            'crear-compra',
            'mostrar-compra',

            //Empleado
            'ver-empleado',
            'crear-empleado',
            'editar-empleado',
            'eliminar-empleado',

            //Empresa
            'ver-empresa',
            'update-empresa',

            //Inventario
            'ver-inventario',
            'crear-inventario',

            //Marca
            'ver-marca',
            'crear-marca',
            'editar-marca',
            'eliminar-marca',

            //Movimientos
            'ver-movimiento',
            'crear-movimiento',

            //Presentacione
            'ver-presentacione',
            'crear-presentacione',
            'editar-presentacione',
            'eliminar-presentacione',

            //Producto
            'ver-producto',
            'crear-producto',
            'editar-producto',

            //Perfil 
            'ver-perfil',
            'editar-perfil',

            //Proveedore
            'ver-proveedore',
            'crear-proveedore',
            'editar-proveedore',
            'eliminar-proveedore',

            //Venta
            'ver-venta',
            'crear-venta',
            'mostrar-venta',

            //Roles
            'ver-role',
            'crear-role',
            'editar-role',
            'eliminar-role',

            //User
            'ver-user',
            'crear-user',
            'editar-user',
            'eliminar-user',
        ];

        // 🔥 CREAR PERMISOS SIN DUPLICAR
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 🔥 OPCIONAL PRO: asignar automáticamente al rol Gerente
        $rolGerente = Role::firstOrCreate(['name' => 'Gerente']);

        $rolGerente->givePermissionTo([
            'ver-todas-cajas',
            'ver-venta',
            'ver-movimiento',
            'ver-caja'
        ]);
    }
}