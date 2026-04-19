@extends('layouts.app')

@section('title','Notificaciones')

@section('content')
<div class="container mt-4">
    <h3>🔔 Todas las notificaciones</h3>

    <div class="card">
        <div class="card-body">

            @forelse ($notificaciones as $notification)
                <div class="border-bottom p-2 {{ $notification->read_at ? '' : 'bg-light' }}">
                    
                    <strong>
                        {{ $notification->data['mensaje'] ?? 'Notificación' }}
                    </strong>
                    
                    <br>

                    <small>
                        Vence: {{ $notification->data['fecha_vencimiento'] ?? '' }}
                    </small>

                    <br>

                    <small class="text-muted">
                        {{ $notification->created_at->diffForHumans() }}
                    </small>

                </div>
            @empty
                <p>No hay notificaciones</p>
            @endforelse

        </div>
    </div>
</div>
@endsection