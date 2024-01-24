<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ImptDetImportacionController;
use App\Http\Controllers\bitacoraController;

class ImptImportacionController extends Controller
{
    //
    public function guardarImportacion(Request $request)
    {
        // Mensajes de Errores Personalizados
        $mensajesErrores = [
            'impTC.numeric' => 'El campo Tipo de Cambio debe ser un número.',
            'provId.numeric' => 'El campo ID de proveedor debe ser un número.',
            'almId.numeric' => 'El campo ID de almacén debe ser un número.',
            'impDescripcion.max' => 'El campo debe ser menor a 1000 caracteres',
            'ttxId.required' => 'El campo Tipo Transaccion es Requerido',
            'userId.numeric' => 'El campo ID de usuario debe ser un número.',
            'impFechaElaboracion.required' => 'El Campo Fecha Elaboracion es Requerido',
            'impEstadoImportacion.required' => 'El Campo Estado Importación es Requerido',
            'impActivo.required' => 'El campo Activo es requerido.',
            'impFechaCreacion.required' => 'El campo Fecha de Creación es requerido.',

        ];


        DB::beginTransaction();
        try {
            // Validación de campos
            $datosValidados = $request->validate([
                'impTC' => 'numeric',
                'provId' => 'numeric',
                'almId' => 'numeric',
                'impDescripcion' => 'max:1000',
                'ttxId' => 'required',
                'impFechaElaboracion' => 'required',
                'impEstadoImportacion' => 'required',
                'userId' => 'numeric',
                'impActivo' => 'required',
                'impFechaCreacion' => 'required',

            ], $mensajesErrores);

            // Verificar si existen importaciones
            $impNumero = DB::table('imptimportacion')->max('impNumero');

            if ($impNumero === null) {
                // No hay importaciones, generar el primer número de importación
                $nuevoImpNumero = 'IM000001';
            } else {
                // Generar el nuevo número de importación en el formato "IMP000000"
                $nuevoImpNumero = 'IM' . str_pad((int)substr($impNumero, 2) + 1, 6, '0', STR_PAD_LEFT);
            }

            //obtener el detalle de la importacion de productos
            $detalleImportacion = $request->detalleImportacion;   //esto se descomenta para probar en postman
            // $detalleImportacion = json_decode($request->input('detallesVenta'), true);


            $tablaImportacion = 'imptimportacion';
            $impId = DB::table($tablaImportacion)->insertGetId([
                'impNumero' => $nuevoImpNumero,
                'impTC' => $datosValidados['impTC'],
                'provId' => $datosValidados['provId'],
                'almId' => $datosValidados['almId'],
                'impDescripcion' => $datosValidados['impDescripcion'],
                'ttxId' => $datosValidados['ttxId'],
                'impFechaElaboracion' => $datosValidados['impFechaElaboracion'],
                'impEstadoImportacion' => $datosValidados['impEstadoImportacion'],
                'userId' => $datosValidados['userId'],
                'impActivo' => 1,
                'impFechaCreacion' => now(),

            ]);
      
            //Insertar detalles de los Producto Importados en la tabla ImptDetImportacion
            $ImptDetImportacion = new ImptDetImportacionController;

            //code...
            foreach ($detalleImportacion as $detalle) {

                $detalle['impId'] = $impId;
                $status = $ImptDetImportacion->guardarDetalleImportacion(new Request($detalle));
                $statusCode = $status->getStatusCode();
                $content = $status->getContent();
                if ($statusCode == 422 || $statusCode === 500) {
                    DB::rollBack();
                    return response()->json(['error' =>  $content], 422);
                };
            }

            $bitacoraController = new bitacoraController();
            $bitacoraController->insertarBitacora($tablaImportacion, $impId, $datosValidados['userId'], 'Creación de registro', 'Nueva Importación' . "-" . $nuevoImpNumero);

            DB::commit();
            return response()->json(['Mensaje' => 'Importación registrada con éxito'], 201);
        } catch (\Illuminate\Validation\ValidationException  $e) {
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
