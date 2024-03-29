<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ImptDetImportacionController;
use App\Http\Controllers\bitacoraController;
use Illuminate\Database\QueryException;

class ImptImportacionController extends Controller
{
    //
    public function guardarImportacion(Request $request)
    {
        // Mensajes de Errores Personalizados
        $mensajesErrores = [
            'impTC.numeric' => 'El campo Tipo de Cambio debe ser un número.',
            'impTC.required' => 'El campo Tipo de Cambio es requerido.',
            'provId.numeric' => 'El campo ID de proveedor debe ser un número.',
            'provId.required' => 'El campo ID de proveedor es requerido.',
            'almId.numeric' => 'El campo ID de almacén debe ser un número.',
            'almId.required' => 'El campo almacen es requerido.',
            'impDescripcion.max' => 'El campo debe ser menor a 1000 caracteres',
            'impDescripcion.required'=>'El campo Descripción es requerido',
            'ttxId.required' => 'El campo Tipo Transaccion es Requerido',
            'userId.numeric' => 'El campo ID de usuario debe ser un número.',
            'impFechaElaboracion.required' => 'El Campo Fecha Elaboracion es Requerido',
            'impEstadoImportacion.required' => 'El Campo Estado Importación es Requerido',
            // 'impActivo.required' => 'El campo Activo es requerido.',
            // 'impFechaCreacion.required' => 'El campo Fecha de Creación es requerido.',

        ];


        DB::beginTransaction();
        try {
            // Validación de campos
            $datosValidados = $request->validate([
                'impTC' => 'numeric',
                'provId' => 'numeric|required',
                'almId' => 'numeric|required',
                'impDescripcion' => 'required|max:1000',
                'ttxId' => 'required',
                'impFechaElaboracion' => 'required',
                'impEstadoImportacion' => 'required',
                'userId' => 'numeric',
                // 'impActivo' => 'required',
                // 'impFechaCreacion' => 'required',

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
            // $detalleImportacion = $request->detalleImportacion;   //esto se descomenta para probar en postman
            $detalleImportacion = json_decode($request->input('detalleImportacion'), true);


            $tablaImportacion = 'imptimportacion';
            $impId = DB::table($tablaImportacion)->insertGetId([
                'impNumero' => $nuevoImpNumero, // se genera de forma automatica
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
                $detalle['dImpFechaCreacion'] = $datosValidados['impFechaElaboracion'];
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
            return response()->json(['mensaje' => 'Importación ' . $nuevoImpNumero . ' registrada con éxito', 'cjtReferencia' => $nuevoImpNumero], 201);
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

    public function listaImportacion(Request $request)
    {
        try {
            // Recibe los parámetros de paginación de la solicitud
            $page = $request->input('page', 1);
            $perPage = $request->input('perPage', 5);

            $results = DB::table('imptimportacion')
                ->select(
                    'imptimportacion.impId',
                    'imptimportacion.impNumero',
                    'imptimportacion.impTC',
                    'gntproveedor.provID',
                    'gntproveedor.provNombre',
                    'imptimportacion.impFechaElaboracion',
                    'imptimportacion.impEstadoImportacion',
                    'imptimportacion.impDescripcion',
                    'intalmacen.almNombreAlmacen'
                )
                ->join('gntproveedor', 'imptimportacion.provId', '=', 'gntproveedor.provID')
                ->join('intalmacen', 'imptimportacion.almId', '=', 'intalmacen.almId')
                ->join('gnttipotxn', 'imptimportacion.ttxId', '=', 'gnttipotxn.ttxId')
                ->join('users', 'imptimportacion.userId', '=', 'users.id')
                ->where('imptimportacion.impActivo', 1)
                ->orderBy('imptimportacion.impNumero', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            if ($results->isEmpty()) {
                // Mensaje si no se encuentra ningún dato
                return response()->json(['mensaje' => 'No se encontraron datos de importación. '], 201);
            } else {
                // Aquí puedes realizar cualquier operación adicional con los resultados obtenidos
                return response()->json($results);
            }
        } catch (QueryException $e) {
            // Mensaje en caso de error de servidor
            return "Error de servidor: " . $e->getMessage();
        }
    }


    public function TraeImportacion(Request $request)
    {
        $numeroImportacion = $request->input('impNumero');

        try {
            $resultados = DB::table('imptimportacion')
                ->select(
                    'imptimportacion.impId',
                    'imptimportacion.impNumero',
                    'imptimportacion.impTC',
                    'gntproveedor.provID',
                    'gntproveedor.provNombre',
                    'intalmacen.almId',
                    'intalmacen.almNombreAlmacen',
                    'imptimportacion.impDescripcion',
                    'imptimportacion.impFechaElaboracion',
                    'imptimportacion.impEstadoImportacion',
                )
                ->join('gntproveedor', 'imptimportacion.provId', '=', 'gntproveedor.provID')
                ->join('imptdetimportacion', 'imptimportacion.impId', '=', 'imptdetimportacion.impId')
                ->join('intalmacen','imptimportacion.almId','=','intalmacen.almId')
                ->where('imptdetimportacion.dImpActivo', 1)
                ->where('imptimportacion.impNumero', $numeroImportacion)
                ->distinct()
                ->get();

            $responseArray = [];

            foreach ($resultados as $resultado) {
                $importacion = [
                    'impId' => $resultado->impId,
                    'impNumero' => $resultado->impNumero,
                    'impTC' => $resultado->impTC,
                    'provID' => $resultado->provID,
                    'provNombre' => $resultado->provNombre,
                    'almId'=>$resultado->almId,
                    'almNombreAlmacen'=>$resultado->almNombreAlmacen,
                    'impDescripcion'=>$resultado->impDescripcion,
                    'impFechaElaboracion' => $resultado->impFechaElaboracion,
                    'impEstadoImportacion' => $resultado->impEstadoImportacion,
                    'detalles' => [],
                ];

                $detalles = DB::table('intarticulo')
                    ->select(
                        'intarticulo.artId',
                        'intarticulo.artCodigo',
                        'intarticulo.artNombre',
                        'imptdetimportacion.impId as detImpId',
                        'imptdetimportacion.dImpCantidad',
                        'imptdetimportacion.dImpPrecioUnitario',
                        'imptdetimportacion.dImpCostoUnitario'
                    )
                    ->join('imptdetimportacion', 'intarticulo.artId', '=', 'imptdetimportacion.artId')
                    ->where('imptdetimportacion.dImpActivo', 1)
                    ->where('imptdetimportacion.impId', $resultado->impId)
                    ->get();

                foreach ($detalles as $detalle) {
                    $importacion['detalles'][] = [
                        'artId' => $detalle->artId,
                        'artCodigo' => $detalle->artCodigo,
                        'artNombre' => $detalle->artNombre,
                        'detImpId' => $detalle->detImpId,
                        'dImpCantidad' => $detalle->dImpCantidad,
                        'dImpPrecioUnitario' => $detalle->dImpPrecioUnitario,
                        'dImpCostoUnitario' => $detalle->dImpCostoUnitario,
                    ];
                }

                $responseArray[] = $importacion;
            }

            return response()->json($responseArray);
        }  catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Error de base de datos: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Error de validación: ' . $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }
}
