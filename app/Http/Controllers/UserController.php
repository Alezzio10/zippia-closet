<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;



class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:8',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        // Rol por defecto para registros públicos
        try {
            $user->assignRole('CLIENTE');
        } catch (\Exception $e) {
            // si el rol no existe, igual devolvemos el registro (no bloqueante)
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errores' => $validator->errors(),
            ], 422);
        }

        $credenciales = $request->only('email', 'password');

        if (!$token = Auth::attempt($credenciales)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //traer todos los usuariosph
         return response()->json(User::all());
        

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'apellido' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'rol_id' => 'required'
    ]);

    $user = User::create([
        'name' => $request->name,
        'apellido' => $request->apellido,
        'telefono' => $request->telefono,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'rol_id' => $request->rol_id
    ]);

    return response()->json($user, 201);
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mostrar usuario
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         // Buscar el usuario
    $user = User::find($id);
    if (!$user) {
        return response()->json([
            'message' => 'Usuario no encontrado'
        ], 404);
    }
    // Validar datos que vienen del request
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $id,
        'apellido' => 'sometimes|string|max:255',
        'telefono' => 'sometimes|string|max:20',
        'password' => 'sometimes|string|min:6',
        'rol_id' => 'sometimes'
    ]);
    // Actualizar solo si vienen los datos
    if ($request->name) {
        $user->name = $request->name;
    }
    if ($request->apellido) {
        $user->apellido = $request->apellido;
    }
    if ($request->telefono) {
        $user->telefono = $request->telefono;
    }
    if ($request->email) {
        $user->email = $request->email;
    }
    if ($request->password) {
        $user->password = Hash::make($request->password);
    }
    if ($request->rol_id) {
        $user->rol_id = $request->rol_id;
    }
    //Guardar cambios
    $user->save();
    return response()->json([
        'message' => 'Usuario actualizado correctamente',
        'user' => $user
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //eliminar usuario
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }
         
}

