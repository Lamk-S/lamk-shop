<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class ApiPeruController extends Controller
{
    public function consultar(Request $request): JsonResponse
    {
        $documento = trim($request->query('documento'));
        
        // Inferir el tipo por la longitud: 8 es DNI, 11 es RUC
        $tipo = strlen($documento) === 11 ? 'ruc' : (strlen($documento) === 8 ? 'dni' : null);

        if (!$tipo) {
            return response()->json(['error' => 'El documento debe tener 8 (DNI) o 11 (RUC) dígitos.'], 422);
        }

        $token = env('APIS_PERU_TOKEN');

        if (!$token) {
            return response()->json(['error' => 'Token de API no configurado en el servidor.'], 500);
        }

        // 1. Usar las URLs exactas de dniruc.apisperu.com
        // 2. Pasar el token directamente en la URL como parámetro
        $url = $tipo === 'ruc' 
            ? "https://dniruc.apisperu.com/api/v1/ruc/{$documento}?token={$token}" 
            : "https://dniruc.apisperu.com/api/v1/dni/{$documento}?token={$token}";

        // Hacemos la petición GET (desactivamos verify por si tu entorno local no tiene SSL configurado)
        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['success']) && $data['success'] === false) {
                 return response()->json(['error' => $data['message'] ?? 'Documento no encontrado.'], 404);
            }

            return response()->json($data);
        }

        return response()->json([
            'error' => 'Fallo en la comunicación con APIs Perú.',
            'http_status' => $response->status(),
            'api_response' => $response->json() ?? $response->body()
        ], $response->status());
    }
}