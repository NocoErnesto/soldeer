<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;


class imptConceptogastoController extends Controller
{
    //
    public function listaConceptoGasto()
    {
        try {
            $conceptosGasto = DB::table('imptconceptogasto')
                ->join('impttipogasto', 'imptconceptogasto.tgasId', '=', 'impttipogasto.tgasId')
                ->where('imptconceptogasto.cgasActivo', 1)
                ->select('impttipogasto.tgasNombre as TipoGasto', 'imptconceptogasto.*')
                ->get();

            // Agrupar por categoría utilizando una colección de Laravel
            $conceptosGastoAgrupados = $conceptosGasto->groupBy('TipoGasto');

            // Transformar la colección para tener un formato más adecuado para la respuesta JSON
            $resultadoFinal = $conceptosGastoAgrupados->map(function ($items, $categoria) {
                return [
                    'TipoGasto' => $categoria,
                    'conceptos' => $items,
                ];
            });

            return response()->json($resultadoFinal, 201);
        } catch (QueryException $e) {
            // Manejar la excepción aquí, por ejemplo, loggearla o devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener la lista de conceptos de gasto' . $e->getMessage()], 500);
        }
    }

    public function TraeConceptoGasto(Request $request)
    {

        $id = $request->input('Id');
        $Concepto = DB::table('imptconceptogasto')
            ->join('impttipogasto', 'imptconceptogasto.tgasId', '=', 'impttipogasto.tgasId')
            ->where('cgasId', $id)
            ->Where('cgasActivo', 1)->get();

        if (!$Concepto) {
            return response()->json(['error' => 'Concepto  no encontrada'], 404);
        }

        return response()->json($Concepto, 200);
    }
    public function agregarConceptoGasto(Request $request)
    {
        // Mensajes de Errores Personalizados
        $mensajesErrores = [
            'cgasNombre.required' => 'El campo Nombre del Concepto de Gasto es requerido.',
            'cgasNombre.string' => 'El campo Nombre del Concepto de Gasto debe ser una cadena de texto.',
            'cgasNombre.max' => 'El campo Nombre del Concepto de Gasto no debe exceder los :max caracteres.',
            'tgasId.required' => 'El campo Tipo de Gasto es requerido.',
            'tgasId.numeric' => 'El campo ID del Tipo de Gasto debe ser un número.',
        ];

        try {
            // Validacion de campos
            $datosValidados = $request->validate([
                'cgasNombre' => 'required|string|max:100',
                'tgasId' => 'required|numeric',
            ], $mensajesErrores);

            // Obtener los datos del request
            $cgasNombre = strtoupper($datosValidados['cgasNombre']);
            $tgasId = $datosValidados['tgasId'];

            // Insertar en la base de datos y obtener el ID insertado
            $cgasId = DB::table('imptconceptogasto')->insertGetId([
                'cgasNombre' => $cgasNombre,
                'tgasId' => $tgasId,
                'cgasActivo' => 1,
                // 'cgasFechaCreacion' => now(), // Puedes descomentar esta línea si deseas registrar la fecha de creación
            ]);

            // Responder con éxito
            return response()->json(['mensaje' => 'Concepto de Gasto creado con éxito', 'cgasId' => $cgasId], 201);
        } catch (QueryException $e) {
            // Capturar excepciones de validación y responder con mensajes de error personalizados
            return response()->json(['error' => 'Error al crear el Concepto de Gasto: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar otras excepciones y responder con un mensaje de error general de validación
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        }
    }
    public function modificarConceptoGasto(Request $request)
    {
        // Mensajes de Errores Personalizados
        $mensajesErrores = [
            'cgasNombre.required' => 'El campo Nombre del Concepto de Gasto es requerido.',
            'cgasNombre.string' => 'El campo Nombre del Concepto de Gasto debe ser una cadena de texto.',
            'cgasNombre.max' => 'El campo Nombre del Concepto de Gasto no debe exceder los :max caracteres.',
            'tgasId.required' => 'El campo ID del Tipo de Gasto es requerido.',
            'tgasId.numeric' => 'El campo ID del Tipo de Gasto debe ser un número.',
            'Id.required' => 'El campo ID del Concepto de Gasto es requerido.',
            'Id.numeric' => 'El campo ID del Concepto de Gasto debe ser un número.',
        ];

        try {
            // Validación de campos
            $datosValidados = $request->validate([
                'cgasNombre' => 'required|string|max:100',
                'tgasId' => 'required|numeric',
                'Id' => 'required|numeric',
                'cgasEstado'
            ], $mensajesErrores);

            // Obtener los datos del request
            $cgasNombre = strtoupper($datosValidados['cgasNombre']);
            $tgasId = $datosValidados['tgasId'];
            $id = $datosValidados['Id'];
            $cgasEstado=$datosValidados['cgasEstado'];
            // Actualizar en la base de datos
            DB::table('imptconceptogasto')
                ->where('cgasId', $id)
                ->update([
                    'cgasNombre' => $cgasNombre,
                    'cgasEstado'=>$cgasEstado,
                    'tgasId' => $tgasId,
                    // 'cgasFechaModificacion' => now(), // Puedes descomentar esta línea si deseas registrar la fecha de modificación
                ]);

            // Responder con éxito
            return response()->json(['mensaje' => 'Concepto de Gasto modificado con éxito'], 200);
        } catch (QueryException $e) {
            // Capturar excepciones de modificación y responder con mensajes de error personalizados
            return response()->json(['error' => 'Error al modificar el Concepto de Gasto: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar otras excepciones y responder con un mensaje de error general de validación
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        }
    }

    public function eliminarConceptoGasto(Request $request)
    {
        // Mensajes de Errores Personalizados
        $mensajesErrores = [
            'Id.required' => 'El campo ID del Concepto de Gasto es requerido.',
            'Id.numeric' => 'El campo ID del Concepto de Gasto debe ser un número.',
        ];

        try {
            // Validación de campos
            $datosValidados = $request->validate([
                'Id' => 'required|numeric',
            ], $mensajesErrores);

            // Obtener el ID del concepto de gasto del request
            $id = $datosValidados['Id'];

            // Actualizar 'cgasActivo' a 0 para indicar inactividad
            $affectedRows = DB::table('imptconceptogasto')->where('cgasId', $id)->update(['cgasActivo' => 0]);
            echo "$affectedRows";
            if ($affectedRows === 0) {
                return response()->json(['error' => 'No se encontró el Concepto de Gasto o ya está inactivo.'], 404);
            }

            // Responder con éxito
            return response()->json(['mensaje' => 'Concepto de Gasto eliminado con éxito'], 201);
        } catch (QueryException $e) {
            // Capturar excepciones y responder con mensajes de error personalizados
            return response()->json(['error' => 'Error al eliminar el Concepto de Gasto: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación y responder con un mensaje de error general
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        }
    }
}
