<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index(): JsonResponse
    {
        $dbConnected = true;
        $dbMessage = null;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbConnected = false;
            $dbMessage = config('app.env') === 'local' ? $e->getMessage() : null;
        }

        $payload = [
            'app_name' => config('app.name'),
            'environment' => config('app.env'),
            'debug' => config('app.debug'),
            'timestamp' => now()->toIso8601String(),
            'database' => $dbConnected ? 'connected' : 'disconnected',
        ];

        if ($dbMessage) {
            $payload['database_error'] = $dbMessage;
        }

        $statusCode = $dbConnected ? 200 : 503;

        return response()->json($payload, $statusCode);
    }
}
