<?php

namespace App\Http\Controllers;

use App\Enums\MetodoPagoEnum;
use App\Events\CreateVentaDetalleEvent;
use App\Events\CreateVentaEvent;
use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\ActivityLogService;
use App\Services\ComprobanteService;
use App\Services\EmpresaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ventaController extends Controller
{
    protected EmpresaService $empresaService;

    function __construct(EmpresaService $empresaService)
    {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        $this->middleware('check-caja-aperturada-user', ['only' => ['create', 'store']]);
        $this->middleware('check-show-venta-user', ['only' => ['show']]);
        $this->empresaService = $empresaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $query = Venta::with(['comprobante', 'cliente.persona', 'user'])
            ->latest();

        // 🔥 CONTROL DE PERMISOS (GERENTE vs CAJERO)
        if (!auth()->user()->can('ver-todas-cajas')) {
            $query->where('user_id', auth()->id());
        }

        $ventas = $query->get();

        return view('venta.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ComprobanteService $comprobanteService): View
    {
        $productos = Producto::join('inventario as i', function ($join) {
            $join->on('i.producto_id', '=', 'productos.id');
        })
            ->join('presentaciones as p', function ($join) {
                $join->on('p.id', '=', 'productos.presentacione_id');
            })
            ->select(
                'p.sigla',
                'productos.nombre',
                'productos.codigo',
                'productos.id',
                'i.cantidad',
                'productos.precio'
            )
            ->where('productos.estado', 1)
            ->where('i.cantidad', '>', 0)
            ->get();

        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();

        $comprobantes = $comprobanteService->obtenerComprobantes();
        $optionsMetodoPago = MetodoPagoEnum::cases();
        $empresa = $this->empresaService->obtenerEmpresa();

        return view('venta.create', compact(
            'productos',
            'clientes',
            'comprobantes',
            'optionsMetodoPago',
            'empresa'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentaRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $venta = Venta::create($request->validated());

            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioVenta = $request->get('arrayprecioventa');

            $siseArray = count($arrayProducto_id);
            $cont = 0;

            while ($cont < $siseArray) {
                $venta->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'cantidad' => $arrayCantidad[$cont],
                        'precio_venta' => $arrayPrecioVenta[$cont],
                    ]
                ]);

                CreateVentaDetalleEvent::dispatch(
                    $venta,
                    $arrayProducto_id[$cont],
                    $arrayCantidad[$cont],
                    $arrayPrecioVenta[$cont]
                );

                $cont++;
            }

            CreateVentaEvent::dispatch($venta);

            DB::commit();

            ActivityLogService::log('Creación de una venta', 'Ventas', $request->validated());

            return redirect()->route('movimientos.index', ['caja_id' => $venta->caja_id])
                ->with('success', 'Venta registrada');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear la venta', ['error' => $e->getMessage()]);

            return redirect()->route('ventas.index')
                ->with('error', 'Ups, algo falló');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta): View
    {
        $empresa = $this->empresaService->obtenerEmpresa();
        return view('venta.show', compact('venta', 'empresa'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    /**
     * POS
     */
    public function pos()
    {
        $productos = \DB::table('productos as p')
            ->join('inventario as i', 'i.producto_id', '=', 'p.id')
            ->select(
                'p.id',
                'p.nombre',
                'p.codigo',
                'p.precio',
                'i.cantidad as stock'
            )
            ->where('p.estado', 1)
            ->get();

        $empresa = $this->empresaService->obtenerEmpresa();

        return view('venta.pos', compact('productos', 'empresa'));
    }
}
