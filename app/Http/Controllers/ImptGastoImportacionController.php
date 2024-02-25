<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImptGastoImportacionController extends Controller
{
    //
    public function guardarGastosImportacion(Request $request)
    {
        try {
            $mensajesErrores = [
                'impNumero.required' => 'El campo Número de Importación es requerido.',
            ];
    
            $impNumero = $request->input('impNumero');
            $gastosImportacion = json_decode($request->input('gastosImportacion'), true);
    
            DB::beginTransaction();
            $request->validate([
                'impNumero' => 'required',
            ], $mensajesErrores);
    
            foreach ($gastosImportacion as $tipoGasto) {
                foreach ($tipoGasto['conceptos'] as $concepto) {
                    DB::table('imptgastoimportacion')->insert([
                        'impNumero' => $impNumero,
                        'cgasId' => $concepto['cgasId'],
                        'gImpMonto' => $concepto['precioBs'],
                        'gImpActivo' => 1,
                        'gImpFechaCreacion' => now(),
                    ]);
                }
            }
    
            DB::commit();
            return response()->json(['mensaje' => 'Gastos de importación registrados con éxito'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Excepción de validación (Error de datos)
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Otras excepciones (Error del servidor)
            DB::rollBack();
            return response()->json(['error' => 'Error al realizar la operación: ' . $e->getMessage()], 500);
        }
    }
}
