<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class UserController extends Controller
{
    public function listado()
    {
        return view('usuario.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $usuarios = User::with('rol')->get();
            $valores = [
                'listado' => view('usuario.ajaxListado')->with(compact('usuarios'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarUsuario(Request $request)
    {
        if ($request->ajax()) {
            $usuario_id = $request->input('id');
            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');
            $celular = $request->input('celular');

            if ($usuario_id == "0") {
                $user = new User();
                $user->password = bcrypt($password);
            } else {
                $user = User::find($usuario_id);
                if ($password) {
                    $user->password = bcrypt($password);
                }
            }
            $user->name = $name;
            $user->email = $email;
            $user->save();
            $data = Respuesta::success(null, "Usuario guardado correctamente");
        } else {
            $data = Respuesta::error(null, "Error al guardar el usuario");
        }
        return $data;
    }

    public function eliminarUsuario(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $user = User::find($id);
            if ($user) {
                $user->save();
                $user->delete();
                $data = Respuesta::success(null, "Usuario eliminado correctamente");
            } else {
                $data = Respuesta::error(null, "Usuario no encontrado");
            }
        } else {
            $data = Respuesta::error(null, "Error al eliminar el usuario");
        }
        return $data;
    }
}
