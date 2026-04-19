<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerificarVencimientos extends Command
{
    protected $signature = 'productos:vencimiento';
    protected $description = 'Verifica productos próximos a vencer';

public function handle()
{
    $hoy = now();

    // 🟡 Por vencer (hoy → 7 días)
    $porVencer = \DB::table('inventario as i')
        ->join('productos as p', 'p.id', '=', 'i.producto_id')
        ->select('p.nombre', 'i.fecha_vencimiento')
        ->whereBetween('i.fecha_vencimiento', [$hoy, $hoy->copy()->addDays(7)])
        ->get();

    // 🔴 Vencidos
    $vencidos = \DB::table('inventario as i')
        ->join('productos as p', 'p.id', '=', 'i.producto_id')
        ->select('p.nombre', 'i.fecha_vencimiento')
        ->whereDate('i.fecha_vencimiento', '<', $hoy)
        ->get();

    $users = \App\Models\User::all();

    // 🔴 Notificar vencidos
   foreach ($vencidos as $producto) {
    foreach ($users as $user) {

        $existe = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('type', \App\Notifications\ProductoVencido::class)
            ->whereJsonContains('data->mensaje', 'Producto VENCIDO: ' . $producto->nombre)
            ->exists();

        if (!$existe) {
            $user->notify(new \App\Notifications\ProductoVencido($producto, 'vencido'));
        }
    }
}

    // 🟡 Notificar por vencer
    foreach ($porVencer as $producto) {
    foreach ($users as $user) {

        $existe = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('type', \App\Notifications\ProductoVencido::class)
            ->whereJsonContains('data->mensaje', 'Producto por vencer: ' . $producto->nombre)
            ->exists();

        if (!$existe) {
            $user->notify(new \App\Notifications\ProductoVencido($producto, 'por_vencer'));
        }
    }
}

    $this->info('Notificaciones enviadas correctamente');
}
}