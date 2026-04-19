<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductoVencido extends Notification
{
    use Queueable;

    public $producto;
    public $tipo;

    public function __construct($producto, $tipo)
    {
        $this->producto = $producto;
        $this->tipo = $tipo;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'mensaje' => $this->tipo === 'vencido'
                ? '🔴 Producto VENCIDO: ' . $this->producto->nombre
                : '🟡 Producto por vencer: ' . $this->producto->nombre,

            'fecha_vencimiento' => $this->producto->fecha_vencimiento,
        ];
    }
}