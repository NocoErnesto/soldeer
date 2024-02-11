<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntalmacenController extends Controller
{
    //

    public function agregarAlmacen(Request $request)
    {
        $almNombreAlmacen = strtoupper($request->input('almNombreAlmacen'));
        $almPrefijo = $request->input('almPrefijo');
        $userid = $request->input('userid');
        $almHabilitado = $request->input('almHabilitado') ? 1 : 0; // Suponiendo que es una casilla de verificación
        $almActivo = 1; // Por defecto, el almacén se crea activo

        try {
            $almId = DB::table('intalmacen')->insertGetId([
                'almNombreAlmacen' => $almNombreAlmacen,
                'almPrefijo' => $almPrefijo,
                'userid' => $userid,
                'almHabilitado' => $almHabilitado,
                'almActivo' => $almActivo,
                'almFechaCreacion' => now(),
            ]);

            return response()->json(['mensaje' => 'Almacén agregado con éxito', 'almId' => $almId], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al agregar almacén: ' . $e->getMessage()], 500);
        }
    }

    public function modificarAlmacen(Request $request)
    {
        $almId = $request->input('almId');
        $almNombreAlmacen = strtoupper($request->input('almNombreAlmacen'));
        $almPrefijo = $request->input('almPrefijo');
        $almHabilitado = $request->input('almHabilitado') ? 1 : 0;

        try {
            DB::table('intalmacen')
                ->where('almId', $almId)
                ->update([
                    'almNombreAlmacen' => $almNombreAlmacen,
                    'almPrefijo' => $almPrefijo,
                    'almHabilitado' => $almHabilitado,
                ]);

            return response()->json(['mensaje' => 'Almacén modificado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al modificar almacén: ' . $e->getMessage()], 500);
        }
    }

    public function listarAlmacen()
    {
        $almacenes = DB::table('intalmacen')->where('almActivo', 1)->get();
        return response()->json($almacenes, 200);
    }

    public function traerAlmacen(Request $request)
    {
        $almId = $request->input('almId');
        $almacen = DB::table('intalmacen')
            ->where('almId', $almId)
            ->where('almActivo', 1)
            ->first();

        if (!$almacen) {
            return response()->json(['error' => 'Almacén no encontrado'], 404);
        }

        return response()->json($almacen, 200);
    }

    public function eliminarAlmacen(Request $request)
    {
        DB::beginTransaction();
        try {
            $tabla = 'intalmacen';
            DB::table($tabla)
                ->where('almId', $request->almId)
                ->update([
                    'almActivo' => 0,
                ]);

            DB::commit();
            return response()->json(['mensaje' => 'Operación realizada con éxito'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se logró realizar la operación: ' . $e->getMessage()], 409);
        }
    }
}
