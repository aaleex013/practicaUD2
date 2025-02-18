<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class JsonController extends Controller
{
    /**
     * Lista todos los ficheros JSON de la carpeta storage/app/json.
     * Se verifica que el contenido sea JSON válido.
     */
    public function index(): JsonResponse
    {
        $files = collect(Storage::disk('local')->files('json'))
            ->filter(function ($file) {
                $content = Storage::get($file);
                json_decode($content);
                return json_last_error() === JSON_ERROR_NONE;
            })
            ->map(fn($file) => basename($file))
            ->values();

        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => $files,
        ]);
    }

    /**
     * Guarda un nuevo archivo JSON en storage/app/json.
     */
    public function store(Request $request): JsonResponse
    {
        $filename = $request->input('filename');
        $content = $request->input('content');

        if (!$filename || !$content) {
            return response()->json(['mensaje' => 'Parámetros inválidos'], 422);
        }

        if (!str_ends_with(strtolower($filename), '.json')) {
            $filename .= ".json";
        }

        $filePath = "json/" . $filename;

        if (Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        if (json_decode($content) === null) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        Storage::disk('local')->put($filePath, $content);

        return response()->json(['mensaje' => 'Fichero JSON guardado exitosamente']);
    }

    /**
     * Muestra el contenido de un archivo JSON.
     */
    public function show(string $id): JsonResponse
    {
        $filePath = "json/" . $id;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $content = Storage::get($filePath);

        return response()->json([
            'mensaje' => 'Fichero leído con éxito',
            'contenido' => json_decode($content, true),
        ]);
    }

    /**
     * Actualiza un archivo JSON existente.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $filePath = "json/" . $id;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        $content = $request->input('content');

        if (!$content) {
            return response()->json(['mensaje' => 'Parámetros inválidos'], 422);
        }

        if (json_decode($content) === null) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        Storage::disk('local')->put($filePath, $content);

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente']);
    }

    /**
     * Elimina un archivo JSON existente.
     */
    public function destroy(string $id): JsonResponse
    {
        $filePath = "json/" . $id;

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        Storage::disk('local')->delete($filePath);

        return response()->json(['mensaje' => 'Fichero eliminado exitosamente']);
    }
}
