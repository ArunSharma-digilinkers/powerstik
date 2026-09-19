<?php

namespace App\Http\Controllers;

use App\Support\Ops;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpsController extends Controller
{
    public function deploy(Request $request): JsonResponse
    {
        $token = config('powerstik.ops_token');

        abort_if(blank($token) || ! hash_equals($token, (string) $request->bearerToken()), 404);

        return response()->json(['ok' => true, 'output' => Ops::run('deploy')]);
    }
}
