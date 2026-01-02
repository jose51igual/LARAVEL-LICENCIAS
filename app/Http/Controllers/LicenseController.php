<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use Carbon\Carbon;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $licenses = License::orderBy('created_at', 'desc')->paginate(15);
        return view('licenses.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('licenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $expiration_date = Carbon::now()->addMonths((int)$validated['duration']);
        
        // Generar clave de licencia única
        $license_key = $this->generateLicenseKey();

        License::create([
            'license_key' => $license_key,
            'name' => $validated['name'],
            'domain' => $validated['domain'],
            'duration' => $validated['duration'],
            'expiration_date' => $expiration_date,
            'is_active' => true,
        ]);

        return redirect()->route('licenses.index')->with('success', 'Licencia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(License $license)
    {
        return view('licenses.show', compact('license'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(License $license)
    {
        return view('licenses.edit', compact('license'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, License $license)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $license->update($validated);

        return redirect()->route('licenses.index')->with('success', 'Licencia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(License $license)
    {
        $license->delete();
        return redirect()->route('licenses.index')->with('success', 'Licencia eliminada exitosamente.');
    }

    /**
     * Generate a unique license key
     */
    private function generateLicenseKey()
    {
        do {
            // Generar una clave en formato: XXXX-XXXX-XXXX-XXXX-XXXX
            $key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 20));
            $formatted_key = substr($key, 0, 4) . '-' . 
                           substr($key, 4, 4) . '-' . 
                           substr($key, 8, 4) . '-' . 
                           substr($key, 12, 4) . '-' . 
                           substr($key, 16, 4);
        } while (License::where('license_key', $formatted_key)->exists());

        return $formatted_key;
    }
}
