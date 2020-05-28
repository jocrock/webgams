<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarProducto;
use App\Models\Productos;
use Illuminate\Broadcasting\Channel;
//use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Validator;
use ProductosSeeder;

class ProductosController extends Controller
{
    //-----------------MUESTRA TODOS LOS PRODUCTOS-----------------------------------------
    public function index(Request $request)
    {
        $id = $request->get('buscar');
        $productos = Productos::Nombres($id)->paginate(10);
        return view('productos/index', ['productos' => $productos]); //response(compact('productos'));
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
        $foto = $request->file('FOTO')->store('public/productos');
        $product = Productos::create(
            [
                'CODPROD' => $cod, //$faker->unique()->bothify('??###'),
                'NOMBRE' => $request['NOMBRE'],
                'DESCRIPCION' => $request['DESCRIPCION'],
                'STOCK' => $request['STOCK'],
                'FOTO' => $foto,
                'TIPO' => $request['groupOfDefaultRadios']

            ]
        );
        return redirect()->route('productos.create', $product->id)
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
    public function update(ValidarProducto $request, $id)
    {
        $entrada = $request->all();
        try {
            $producto = Productos::find($id);
            if ($producto == false) {
                return response()->json([
                    'ok' => false,
                    'error' => "No se encontro el producto"
                ]);
            }
            $producto->update($entrada);
            return response()->json([
                'ok' => true,
                'mensaje' => 'Datos actualizados correctamente'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'ok' => false,
                'error' => $ex->getMessage()
            ]);
        }
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
