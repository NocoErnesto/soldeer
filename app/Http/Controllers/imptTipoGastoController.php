<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class imptTipoGastoController extends Controller
{
    //
    public $mensajesErrores = [
        'tgasNombre.required' => 'El campo Nombre del Tipo de Gasto es requerido.',
        'tgasNombre.string' => 'El campo Nombre del Tipo de Gasto debe ser una cadena de texto.',
        'tgasNombre.max' => 'El campo Nombre del Tipo de Gasto no debe exceder los :max caracteres.',
    ];
    public function listaTipogasto()
    {
        try {
            $tiposGasto = DB::table('impttipogasto')->where('tgasAtivo', 1)->get();

            return response()->json($tiposGasto, 200);
        } catch (QueryException $e) {
            // Manejar la excepción aquí, por ejemplo, loggearla o devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener la lista de tipos de gasto'], 500);
        }
    }
    public function TraerTipoGasto(Request $request)
    {

        $id = $request->input('Id');
        $tipoGasto = DB::table('impttipogasto')
            ->where('tgasId', $id)
            ->Where('tgasAtivo', 1)->get();

        if (!$tipoGasto) {
            return response()->json(['error' => 'Tipo Gasto no encontrada'], 404);
        }

        return response()->json($tipoGasto, 200);
    }
    public function agregarTipoGasto(Request $request)
    {
        // Mensajes de Errores Personalizados

        try {
            // Validacion de campos
            $datosValidados = $request->validate([
                'tgasNombre' => 'required|string|max:200',
            ], $this->mensajesErrores);

            // Obtener el nombre del tipo de gasto del request y convertirlo a mayúsculas
            $tgasNombre = strtoupper($datosValidados['tgasNombre']);

            // Insertar en la base de datos y obtener el ID insertado
            $tgasId = DB::table('impttipogasto')->insertGetId([
                'tgasNombre' => $tgasNombre,
                'tgasAtivo' => 1,
                // 'tgasFechaCreacion' => now(), // Puedes descomentar esta línea si deseas registrar la fecha de creación
            ]);

            // Responder con éxito
            return response()->json(['mensaje' => 'Tipo de Gasto creado con éxito', 'tgasId' => $tgasId], 201);
        } catch (QueryException $e) {
            // Capturar excepciones de validación y responder con mensajes de error personalizados
            return response()->json(['error' => 'Error al crear el Tipo de Gasto: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar otras excepciones y responder con un mensaje de error general
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        }
    }
    public function modificarTipoGasto(Request $request)
    {
        // Mensajes de Errores Personalizados
        $mensajesErrores = [
            'tgasNombre.required' => 'El campo Nombre del Tipo de Gasto es requerido.',
            'tgasNombre.string' => 'El campo Nombre del Tipo de Gasto debe ser una cadena de texto.',
            'tgasNombre.max' => 'El campo Nombre del Tipo de Gasto no debe exceder los :max caracteres.',

        ];

        try {
            // Validación de campos
            $datosValidados = $request->validate([
                'tgasNombre' => 'required|string|max:200',
                'Id' => 'required'
            ], $mensajesErrores);

            // Obtener el nombre del tipo de gasto del request y convertirlo a mayúsculas
            $tgasNombre = strtoupper($datosValidados['tgasNombre']);
            $id = $datosValidados['Id'];
            // Actualizar en la base de datos
            DB::table('impttipogasto')
                ->where('tgasId', $id)
                ->update([
                    'tgasNombre' => $tgasNombre,
                    // 'tgasFechaModificacion' => now(), // Puedes descomentar esta línea si deseas registrar la fecha de modificación
                ]);

            // Responder con éxito
            return response()->json(['mensaje' => 'Tipo de Gasto modificado con éxito'], 200);
        } catch (QueryException $e) {
            // Capturar excepciones de modificación y responder con mensajes de error personalizados
            return response()->json(['error' => 'Error al modificar el Tipo de Gasto: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar otras excepciones y responder con un mensaje de error general de validación
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        }
    }
    public function EliminarTipoGasto(Request $request)
    {
        $id = $request->id;
        try {
            // Actualizar 'cliActivo' a 0 para indicar inactividad
            $affectedRows = DB::table('impttipogasto')->where('tgasId', $id)->update(['tgasAtivo' => 0]);

            if ($affectedRows === 0) {
                return response()->json(['error' => 'No se encontró el  Dato o ya está inactivo.'], 404);
            }

            return response()->json(['mensaje' => 'Operación Exitosa'], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error al desactivar el cliente: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Error de validación: ' . $e->errors()], 422);
        }
    }
}
