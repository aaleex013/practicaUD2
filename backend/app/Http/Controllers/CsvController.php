<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;

class CsvController extends Controller
{
    /**
     * Lista todos los ficheros CSV de la carpeta storage/app/csv.
     */
    public function index(): JsonResponse
    {
        $files = collect(Storage::disk('local')->files("csv"))
            ->filter(fn($file) => Str::endsWith(strtolower($file), '.csv'))
            ->map(fn($file) => basename($file))
            ->values();

        return response()->json([
            'mensaje' => 'Listado de ficheros',
            'contenido' => $files,
        ], 200);
    }

    /**
     * Guarda un nuevo archivo CSV en storage/app/csv.
     */
    public function store(Request $request): JsonResponse
    {
        $filename = $request->input('filename');
        $content = $request->input('content');

        if (!$filename || !$content) {
            return response()->json(['mensaje' => 'Parámetros inválidos'], 422);
        }

        if (!Str::endsWith(strtolower($filename), '.csv')) {
            $filename .= ".csv";
        }

        $filePath = "csv/" . $filename;

        if (Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        Storage::disk('local')->put($filePath, $content);
        return response()->json(['mensaje' => 'Fichero CSV guardado exitosamente']);
    }

    /**
     * Muestra el contenido de un archivo CSV como JSON.
     */
    public function show(string $id): JsonResponse
    {
        $filePath = "csv/" . $id;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $content = Storage::disk('local')->get($filePath);
        $lines = explode("\n", trim($content));
        $headers = str_getcsv(array_shift($lines));
        $data = array_map(fn($line) => array_combine($headers, str_getcsv($line, )), array_filter($lines));

        return response()->json([
            'mensaje' => 'Fichero leído con éxito',
            'contenido' => $data,
        ]);
    }

    /**
     * Actualiza un archivo CSV existente.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $filePath = "csv/" . $id;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $content = $request->input('content');

        if (!$content) {
            return response()->json(['mensaje' => 'Parámetros inválidos'], 422);
        }

        Storage::disk('local')->put($filePath, $content);
        return response()->json(['mensaje' => 'Fichero actualizado exitosamente']);
    }

    /**
     * Elimina un archivo CSV existente.
     */
    public function destroy(string $id): JsonResponse
    {
        $filePath = "csv/" . $id;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        Storage::disk('local')->delete($filePath);
        return response()->json(['mensaje' => 'Fichero eliminado exitosamente']);
    }
}
