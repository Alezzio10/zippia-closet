<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request){
        $credenciales = $request->only('email','password');
        //evaluamos si no se obtiene un token válido
        if(!$token = Auth::attempt($credenciales)){
           return response()->json([
            'message'=> 'Credenciales inválidas'
           ], 401);     
        }
        //en caso de exitoso retornamos el token
        return $this->responseWithToken($token);
    }
    public function register(Request $request){
      //validamos datos a través de Request
     $validator = Validator::make($request->all(), [
    'name' => 'required|string|max:191',
    'email' => 'required|string|email|max:191|unique:users',
    'password' => 'required|string|min:8|confirmed',
    'apellido' => 'required|string|max:255',
    'telefono' => 'required|string|max:20',
]);
      if($validator->fails()){
          return response()->json($validator->errors(),422);
      }
      //creamos el usuario
      $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => Hash::make($request->password),
           'apellido' => $request->apellido,
            'telefono' => $request->telefono
      ]);

      //Recordatorio--Asignar rol por defecto
      $user->assignRole('CLIENTE');

      //generamos el token
      $token = JWTAuth::fromUser($user);
      //retornamos la respuesta

      return response()->json([
          'message' => 'Usuario registrado correctamente',
          'user' => $user,
          'access_token' => $token,
          'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL() * 60
      ],201);
  }

  protected function responseWithToken($token){
      return response()->json([
          'access_token' => $token,
          'token_type' => 'bearer',
          'user' => auth()->user(),
          'expires_in' => auth()->factory()->getTTL() * 60
      ]);
  }
  public function me(){
  return response()->json(auth()->user());
}

    //método para invalidar un token (logout)
    public function logout(){
    auth()->logout();
    return response()->json([
        'message' => 'Sesión cerrada correctamente'
    ]);
    }

    //método para refrescar el token
    public function refresh(){
    return $this->responseWithToken(auth()->refresh());
    }



//funcion para que funcione lo de guardar datos en el perfil del front
    public function update(Request $request)
{
    $user = auth()->user();

    $validated = $request->validate([
        'name' => 'sometimes|string|max:191',
        'apellido' => 'sometimes|string|max:255',
        'telefono' => 'sometimes|string|max:20',
    ]);

    $user->update($validated);

    // Dirección
    if ($request->has('direccion')) {

        if ($user->direccion) {
            $user->direccion->update($request->direccion);
        } else {
            $direccion = \App\Models\Direccion::create($request->direccion);
            $user->direccion()->associate($direccion);
            $user->save();
        }
    }

    return response()->json([
        'user' => $user->load('direccion')
    ]);
}
}




