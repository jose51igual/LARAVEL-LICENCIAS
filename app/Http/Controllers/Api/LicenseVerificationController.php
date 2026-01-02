<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use Carbon\Carbon;

class LicenseVerificationController extends Controller
{
    /**
     * Verify a license key and domain
     * 
     * Expected POST data:
     * - key: license key
     * - domain: website domain
     */
    public function verify(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'domain' => 'required|string',
        ]);

        $license = License::where('license_key', $request->key)
                         ->where('domain', $request->domain)
                         ->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada o dominio no coincide.',
                'data' => [
                    'valid' => false,
                ],
            ], 404);
        }

        // Update last checked timestamp
        $license->last_checked_at = Carbon::now();
        $license->save();

        // Check if license is expired
        if ($license->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'La licencia ha expirado.',
                'data' => [
                    'valid' => false,
                    'expired' => true,
                    'expiration_date' => $license->expiration_date->format('Y-m-d'),
                ],
            ], 403);
        }

        // Check if license is active
        if (!$license->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'La licencia está inactiva.',
                'data' => [
                    'valid' => false,
                    'active' => false,
                ],
            ], 403);
        }

        // License is valid
        return response()->json([
            'success' => true,
            'message' => 'Licencia válida.',
            'data' => [
                'valid' => true,
                'license' => [
                    'name' => $license->name,
                    'duration' => $license->duration,
                ],
                'expiration_date' => $license->expiration_date->format('Y-m-d'),
                'remaining_days' => $license->remaining_days,
            ],
        ], 200);
    }
}
