<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ImptDetImportacionController extends Controller
{
    public function guardarDetalleImportacion(Request $request)
    {
       

        DB::beginTransaction();

        try {
             // Mensajes de Errores Personalizados
        $mensajesErrores = [

            'impId.numeric' => 'El campo ID de importación debe ser un número.',
            'artId.numeric' => 'El campo ID de artículo debe ser un número.',
            'dImpCantidad.numeric' => 'El campo Cantidad debe ser un número.',
            'dImpPrecioUnitario.regex' => 'El campo Precio Unitario debe ser un número decimal.',
            'dImpCostoUnitario.regex' => 'El campo Costo Unitario debe ser un número decimal.',
            'dImpActivo.required' => 'El campo Activo es requerido.',
            'dImpFechaCreacion.required' => 'El campo Fecha de Creación es requerido',
        ];
            // Validación de campos
            $datosValidados = $request->validate([
        
                'impId' => 'numeric',
                'artId' => 'numeric',
                'dImpCantidad' => 'numeric',
                'dImpPrecioUnitario' => 'regex:/^\d+(\.\d{1,2})?$/',
                'dImpCostoUnitario' => 'regex:/^\d+(\.\d{1,2})?$/',
                'dImpActivo' => 'required',
                'dImpFechaCreacion' => 'required',
                // Agrega los campos adicionales según tus necesidades
            ], $mensajesErrores);

            $tabla = 'imptdetimportacion';
            DB::table($tabla)->insert([
             
                'impId' => $datosValidados['impId'],
                'artId' => $datosValidados['artId'],
                'dImpCantidad' => $datosValidados['dImpCantidad'],
                'dImpPrecioUnitario' => $datosValidados['dImpPrecioUnitario'],
                'dImpCostoUnitario' => $datosValidados['dImpCostoUnitario'],
                'dImpActivo' => $datosValidados['dImpActivo'],
                'dImpFechaCreacion' => $datosValidados['dImpFechaCreacion'],
                // Agrega los campos adicionales según tus necesidades
            ]);

            DB::commit();
            return response()->json(['Mensaje' => 'Detalle de importación registrado con éxito'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar excepciones de validación y responder con mensajes de error personalizados
            DB::rollBack();
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Capturar otras excepciones y responder con un mensaje de error general
            DB::rollBack();
            return response()->json(['error' => 'Error al realizar la operación: ' . $e->getMessage()], 500);
        }
    }
}
