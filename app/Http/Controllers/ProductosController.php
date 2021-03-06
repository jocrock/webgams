<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarProducto;
use App\Models\Productos;

use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Validator;
use ProductosSeeder;
use Illuminate\Support\Facades\Storage;
use PDF;

class ProductosController extends Controller
{
    //-----------------MUESTRA TODOS LOS PRODUCTOS-----------------------------------------
    public function index(Request $request)
    {
        $id = $request->get('buscar');
        $productos = Productos::Nombres($id)->paginate(10);
        return view('productos/index', ['productos' => $productos]); //response(compact('productos'));
    }
    //---------index para usar en React-------------------------
    public function buscar(Request $request)
    {
        $nombre = $request['buscar'];
        $productos = Productos::buscar($nombre, 10);
        return response()->json(compact('productos'));
    }
    public function indexReact()
    {
        $productos = Productos::productosMasConsumidos(20);
        return response()->json(compact('productos'));
    }
    //---------index para usar en React-------------------------
    public function indexCategorias(Request $request)
    {
        $categoria = $request['CATEGORIA'];
        $productos = Productos::productosCategorias($categoria);
        return response()->json(compact('productos'));
    }
    //---------------GENERAR PDF-----------------------------------------------------------
    public function productosPDF()
    {
        $productos = Productos::get();
        $pdf = PDF::loadView('productos/pdf/productos', compact('productos'))->setPaper('letter', 'landscape');

        return $pdf->stream('productos.pdf');
    }

    //----------------CREA LA PLANTILLA PARA UN NUEVO REGISTRO------------------------------
    public function create()
    {
        return view('productos.create');
    }
    //---------------CREA LA PLANTILLA PARA ACTUALIZAR UN REGISTRO--------------------------
    public function edit(Productos $producto)
    {
        return view('productos.edit', compact('producto'));
    }
    //-------------------------GUARDAR UN NUEVO REGISTRO------------------------------------
    public function store(ValidarProducto $request)
    {

        //try {
        //$faker = $this->getFaker();
        $alfa = 'abcdefghijklmnopqrstuvwxyz';
        $num = '0123456789';
        $cod = substr(str_shuffle($alfa), 2, 3) . substr(str_shuffle($num), 2, 3);
        $foto = null;
        if (isset($request['FOTO']) == true) {
            $foto = $request->file('FOTO')->store('img/productos');
            $foto = 'storage/' . $foto;
        }
        $producto = Productos::create(
            [
                'CODPROD' => $cod,
                'NOMBRE' => $request['NOMBRE'],
                'DESCRIPCION' => $request['DESCRIPCION'],
                'UNIDAD' => $request['UNIDAD'],
                'STOCK' => '0',
                'FOTO' => $foto,
                'TIPO' => $request['groupOfDefaultRadios']

            ]
        );
        return redirect()->route('productos.edit', $producto->id)
            ->with('info', 'Producto guardado con exito');
        /*} catch (\Exception $ex) {
            return redirect()->route('productos.create', $product->id)
                ->with($ex->getMessage());
        }*/
    }
    //---------------------------MUESTRA UN PRODUCTO--------------------------------------
    public function show(Productos $producto)
    {
        return view('productos.show', compact('producto'));
    }
    //-------------------------ACTUALIZA DATOS DE UN PRODUCTO------------------------------
    public function update(ValidarProducto $request, Productos $producto)
    {
        $foto = $producto->FOTO;
        if (isset($request['FOTO']) == true) {
            //Storage::delete($foto);
            $foto = $request->file('FOTO')->store('img/productos');
            $foto = 'storage/' . $foto;
        }
        $producto->update([
            'NOMBRE' => $request['NOMBRE'],
            'DESCRIPCION' => $request['DESCRIPCION'],
            'UNIDAD' => $request['UNIDAD'],
            'FOTO' => $foto,
            'TIPO' => $request['groupOfDefaultRadios']
        ]);

        return redirect()->route('productos.edit', $producto->id)
            ->with('info', 'Producto actualizado con exito');
    }
    //-----------------ACTUALIZAR STOCK-----------------------------------------
    public function actualizar_stock(Request $request)
    {
        $id = $request->get('buscar');
        $productos = Productos::Nombres($id)->paginate(10);
        return view('productos/index', ['productos' => $productos]); //response(compact('productos'));
    }
    //------------------------ELIMINA UN PRODUCTO DEL REGISTRO-----------------------------
    public function destroy($id)
    {
        try {
            $producto = Productos::find($id);
            if ($producto == false) {
                return response()->json([
                    'ok' => false,
                    'error' => 'No se encontro el Productos'
                ]);
            }
            $producto->delete();
            return back()->with('info', 'Eliminado correctamente');
        } catch (\Exception $ex) {
            return back()->with('info', 'Error inesperado al Eliminar (no se elimino)');
        }
    }
}
