<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'dni' => $client->dni,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'full_name' => $client->full_name,
                'phone' => $client->phone,
                'email' => $client->email,
                'address' => $client->address,
                'status' => $client->status,
                'created_at' => $client->created_at->format('d/m/Y H:i'),
                'formatted_id' => 'CL' . str_pad($client->id, 4, '0', STR_PAD_LEFT)
            ];
        });

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|unique:clients,dni',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Client::create($request->all());

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['sales' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('Admin/Clients/Show', [
            'client' => $client
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Admin/Clients/Edit', [
            'client' => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'dni' => 'required|string|size:8|unique:clients,dni,' . $client->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $client->update($request->all());

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    // Método para buscar cliente por DNI usando API
    public function searchByDni(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8'
        ]);

        $dni = $request->dni;

        // Primero buscar en la base de datos local
        $existingClient = Client::findByDni($dni);
        
        if ($existingClient) {
            return response()->json([
                'success' => true,
                'client' => $existingClient,
                'source' => 'database'
            ]);
        }

        // Si no existe localmente, consultar múltiples APIs
        try {
            // Intentar con API 1: ReniecSunat (más confiable)
            $response = Http::timeout(15)->get("https://api.reniec-sunat.com/dni/{$dni}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['nombres']) && isset($data['apellidoPaterno'])) {
                    return response()->json([
                        'success' => true,
                        'client' => [
                            'dni' => $dni,
                            'first_name' => $data['nombres'] ?? '',
                            'last_name' => trim(($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? '')),
                            'full_name' => trim(($data['nombres'] ?? '') . ' ' . ($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? '')),
                        ],
                        'source' => 'api'
                    ]);
                }
            }
            
            // Fallback API 2: DNI API alternativa
            $response2 = Http::timeout(10)->get("https://dniruc.apisperu.com/api/v1/dni/{$dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAZ21haWwuY29tIn0.DKVp0VCXjyrQZus7vgdaVbEG_GJzOqB6wVmOFnPEzpE");
            
            if ($response2->successful()) {
                $data2 = $response2->json();
                
                if (isset($data2['nombres']) && isset($data2['apellidoPaterno'])) {
                    return response()->json([
                        'success' => true,
                        'client' => [
                            'dni' => $dni,
                            'first_name' => $data2['nombres'] ?? '',
                            'last_name' => trim(($data2['apellidoPaterno'] ?? '') . ' ' . ($data2['apellidoMaterno'] ?? '')),
                            'full_name' => trim(($data2['nombres'] ?? '') . ' ' . ($data2['apellidoPaterno'] ?? '') . ' ' . ($data2['apellidoMaterno'] ?? '')),
                        ],
                        'source' => 'api'
                    ]);
                }
            }

            // Fallback API 3: API simple sin límites
            $response3 = Http::timeout(10)->get("https://api.perudevs.com/api/v1/dni/complete", [
                'document' => $dni
            ]);
            
            if ($response3->successful()) {
                $data3 = $response3->json();
                
                if (isset($data3['result']) && isset($data3['result']['name'])) {
                    $fullName = $data3['result']['name'];
                    $nameParts = explode(' ', $fullName);
                    
                    // Asumir que los primeros nombres son nombres y el resto apellidos
                    $firstName = implode(' ', array_slice($nameParts, 0, 2));
                    $lastName = implode(' ', array_slice($nameParts, 2));
                    
                    return response()->json([
                        'success' => true,
                        'client' => [
                            'dni' => $dni,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'full_name' => $fullName,
                        ],
                        'source' => 'api'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'DNI no encontrado en RENIEC. Verifique que el número sea correcto.'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error consultando DNI: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar el DNI. Intente nuevamente en unos momentos.'
            ], 500);
        }
    }

    // Método para crear cliente desde venta
    public function createFromSale(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8|unique:clients,dni',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $client = Client::create($request->all());

        return response()->json([
            'success' => true,
            'client' => $client
        ]);
    }
}
