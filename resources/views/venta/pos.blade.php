@extends('layouts.app')

@section('title','POS')

@push('css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid vh-100 p-0">

<div class="row g-0 h-100">

    <!-- IZQUIERDA -->
    <div class="col-md-8 p-3">

        <h4>🛒 Venta rápida</h4>

        <input type="text" id="codigo_barra"
               class="form-control form-control-lg mb-3"
               placeholder="Escanear código..."
               autofocus>

        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>Precio</th>
                    <th>Sub</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="detalle"></tbody>
        </table>

    </div>

    <!-- DERECHA -->
    <div class="col-md-4 bg-success text-white d-flex flex-column">

        <div class="p-4">

            <h6>
                Subtotal:
                <span id="subtotal">0</span>
                {{$empresa->moneda->simbolo}}
            </h6>

            <h6>
                {{$empresa->abreviatura_impuesto}} ({{$empresa->porcentaje_impuesto}}%):
                <span id="igv">0</span>
                {{$empresa->moneda->simbolo}}
            </h6>

            <h2>
                Total:
                <span id="total">0</span>
                {{$empresa->moneda->simbolo}}
            </h2>

            <!-- INPUTS -->
            <input type="hidden" id="inputSubtotal">
            <input type="hidden" id="inputImpuesto">
            <input type="hidden" id="inputTotal">

        </div>

        <div class="mt-auto p-3 bg-dark">

            <select id="metodo_pago" class="form-control mb-2">
                <option value="EFECTIVO">EFECTIVO</option>
                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                <option value="CREDITO">CRÉDITO</option>
            </select>

            <input type="number" id="recibido"
                   class="form-control mb-2"
                   placeholder="Recibido">

            <div class="mb-2">
                <strong>Vuelto:</strong>
                <span id="vuelto">0</span>
            </div>

            <button class="btn btn-primary w-100"
                    onclick="guardarVenta()">
                FINALIZAR
            </button>

        </div>

    </div>

</div>
</div>
@endsection


@push('js')
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

let productos = @json($productos);
let carrito = [];

let subtotal = 0;
let igv = 0;
let total = 0;

// 🔥 DINÁMICO
const impuesto = {{ $empresa->porcentaje_impuesto }};


// ESCANEAR
$('#codigo_barra').keypress(function(e){
    if(e.which == 13){

        let codigo = $(this).val().trim();

        let producto = productos.find(p => String(p.codigo) === codigo);

        if(producto){
            agregar(producto);
        }else{
            alert("Producto no encontrado");
        }

        $(this).val('');
    }
});


// AGREGAR
function agregar(producto){

    let existente = carrito.find(p => p.id === producto.id);

    if(existente){

        if(existente.cantidad + 1 > producto.stock){
            alert("Sin stock suficiente");
            return;
        }

        existente.cantidad++;

    }else{

        if(producto.stock <= 0){
            alert("Sin stock");
            return;
        }

        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: producto.precio,
            cantidad: 1,
            stock: producto.stock
        });
    }

    render();
}


// RENDER
function render(){

    let html = '';
    subtotal = 0;

    carrito.forEach((p, i)=>{
        let sub = p.cantidad * p.precio;
        subtotal += sub;

        html += `
        <tr>
            <td>${p.nombre}</td>
            <td>${p.cantidad}</td>
            <td>${p.precio} {{$empresa->moneda->simbolo}}</td>
            <td>${sub.toFixed(2)}</td>
            <td>
                <button onclick="eliminar(${i})" class="btn btn-danger btn-sm">X</button>
            </td>
        </tr>
        `;
    });

    igv = subtotal * (impuesto / 100);
    total = subtotal + igv;

    subtotal = parseFloat(subtotal.toFixed(2));
    igv = parseFloat(igv.toFixed(2));
    total = parseFloat(total.toFixed(2));

    $('#detalle').html(html);
    $('#subtotal').text(subtotal.toFixed(2));
    $('#igv').text(igv.toFixed(2));
    $('#total').text(total.toFixed(2));

    // 🔥 INPUTS
    $('#inputSubtotal').val(subtotal);
    $('#inputImpuesto').val(igv);
    $('#inputTotal').val(total);

    calcularVuelto();
}


// ELIMINAR
function eliminar(i){
    carrito.splice(i,1);
    render();
}


// VUELTO
function calcularVuelto(){

    let metodo = $('#metodo_pago').val();
    let recibido = parseFloat($('#recibido').val());

    if(metodo === 'EFECTIVO' && !isNaN(recibido)){
        let vuelto = recibido - total;
        $('#vuelto').text(vuelto.toFixed(2));
    } else {
        $('#vuelto').text("0.00");
    }
}

$('#recibido').on('input', calcularVuelto);

$('#metodo_pago').on('change', function(){

    if($(this).val() === 'EFECTIVO'){
        $('#recibido').prop('disabled', false);
    } else {
        $('#recibido').prop('disabled', true).val('');
        $('#vuelto').text("0.00");
    }

    calcularVuelto();
});


// GUARDAR
function guardarVenta(){

    if(carrito.length == 0){
        alert("No hay productos");
        return;
    }

    let metodo = $('#metodo_pago').val();
    let recibido = parseFloat($('#recibido').val());
    let vuelto = parseFloat($('#vuelto').text()) || 0;

    if(metodo === 'EFECTIVO'){
        if(isNaN(recibido) || recibido < total){
            alert("Monto inválido");
            return;
        }
    } else {
        recibido = total;
        vuelto = 0;
    }

    $.post("{{ route('ventas.store') }}", {

        cliente_id: 1,
        comprobante_id: 1,
        metodo_pago: metodo,
        subtotal: subtotal,
        impuesto: igv,
        total: total,
        monto_recibido: recibido,
        vuelto_entregado: vuelto,
        caja_id: 1,

        arrayidproducto: carrito.map(p => p.id),
        arraycantidad: carrito.map(p => p.cantidad),
        arrayprecioventa: carrito.map(p => p.precio)

    }).done(function(){
        alert("Venta registrada");
        location.reload();
    }).fail(function(xhr){
        console.error(xhr.responseText);
        alert("Error al guardar");
    });
}

</script>
@endpush