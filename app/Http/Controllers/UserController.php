<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inversion;
use App\Models\BusinessCustomer;
use App\Models\Entidad;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $usuarios = User::all();

        return view(
            'usuarios.index',
            compact('usuarios')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | INVERSIONES
        |--------------------------------------------------------------------------
        */

        $inversiones = Inversion::all();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $businessCustomers = BusinessCustomer::orderBy(
            'nombre'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | ENTIDADES
        |--------------------------------------------------------------------------
        */

        $entidades = Entidad::orderBy(
            'denominacion_social'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | PERSONAS
        |--------------------------------------------------------------------------
        */

        $clientes = Cliente::orderBy(
            'nombre'
        )->get();


        return view(
            'usuarios.create',
            compact(
                'inversiones',
                'businessCustomers',
                'entidades',
                'clientes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'email' =>
            'required|email|unique:users,email',

            'password' =>
            'required|min:6',

            'role' =>
            'required',

            'estado' =>
            'required',

        ]);


        /*
        |--------------------------------------------------------------------------
        | CREAR USUARIO
        |--------------------------------------------------------------------------
        */

        $usuario = User::create([

            'name' =>
            $request->name,

            'email' =>
            $request->email,

            'password' =>
            Hash::make(
                $request->password
            ),

            'role' =>
            $request->role,

            'estado' =>
            $request->estado,

        ]);


        /*
        |--------------------------------------------------------------------------
        | PERMISOS
        |--------------------------------------------------------------------------
        */

        if ($request->role == 'user') {

            /*
            |--------------------------------------------------------------------------
            | RELACIÓN USUARIO - INVERSIÓN
            |--------------------------------------------------------------------------
            */

            if ($request->has('inversiones')) {

                foreach (
                    $request->inversiones
                    as $inversionId
                ) {

                    DB::table(
                        'user_inversion'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'inversion_id' =>
                        $inversionId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | PERMISOS POR MÓDULO
                    |--------------------------------------------------------------------------
                    */

                    DB::table(
                        'user_inversion_modulos'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'inversion_id' =>
                        $inversionId,

                        'avaluos' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['avaluos']
                        ),

                        'activos' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['activos']
                        ),

                        'servicios' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['servicios']
                        ),

                        'comercial' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['comercial']
                        ),

                        'entidades' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['entidades']
                        ),

                        'estado_resultados' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['estado_resultados']
                        ),

                        'activos_registrales' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['activos_registrales']
                        ),

                        'bitacoras' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['bitacoras']
                        ),

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | BUSINESS CUSTOMERS
            |--------------------------------------------------------------------------
            */

            if ($request->has('business_customers')) {

                foreach (
                    $request->business_customers
                    as $businessCustomerId
                ) {

                    DB::table(
                        'user_business_customer'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'business_customer_id' =>
                        $businessCustomerId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | ENTIDADES
            |--------------------------------------------------------------------------
            */

            if ($request->has('entidades')) {

                foreach (
                    $request->entidades
                    as $entidadId
                ) {

                    DB::table(
                        'user_entidad'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'entidad_id' =>
                        $entidadId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PERSONAS
            |--------------------------------------------------------------------------
            */

            if ($request->has('clientes')) {

                foreach (
                    $request->clientes
                    as $clienteId
                ) {

                    DB::table(
                        'user_cliente'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'cliente_id' =>
                        $clienteId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }
        }


        return redirect('/usuarios')
            ->with(
                'success',
                'Usuario creado correctamente'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $usuario =
            User::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | INVERSIONES
        |--------------------------------------------------------------------------
        */

        $inversiones =
            Inversion::all();


        /*
        |--------------------------------------------------------------------------
        | PERMISOS DE MÓDULOS
        |--------------------------------------------------------------------------
        */

        $permisos =
            DB::table(
                'user_inversion_modulos'
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->get()
            ->keyBy(
                'inversion_id'
            );


        /*
        |--------------------------------------------------------------------------
        | INVERSIONES DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $inversionesUsuario =
            DB::table(
                'user_inversion'
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->pluck(
                'inversion_id'
            )
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $businessCustomers =
            BusinessCustomer::orderBy(
                'nombre'
            )->get();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS CUSTOMERS DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $businessCustomersUsuario =
            DB::table(
                'user_business_customer'
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->pluck(
                'business_customer_id'
            )
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | ENTIDADES
        |--------------------------------------------------------------------------
        */

        $entidades =
            Entidad::orderBy(
                'denominacion_social'
            )->get();


        /*
        |--------------------------------------------------------------------------
        | ENTIDADES DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $entidadesUsuario =
            DB::table(
                'user_entidad'
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->pluck(
                'entidad_id'
            )
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | PERSONAS
        |--------------------------------------------------------------------------
        */

        $clientes =
            Cliente::orderBy(
                'nombre'
            )->get();


        /*
        |--------------------------------------------------------------------------
        | PERSONAS DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $clientesUsuario =
            DB::table(
                'user_cliente'
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->pluck(
                'cliente_id'
            )
            ->toArray();


        return view(
            'usuarios.edit',
            compact(

                'usuario',

                'inversiones',

                'permisos',

                'inversionesUsuario',

                'businessCustomers',

                'businessCustomersUsuario',

                'entidades',

                'entidadesUsuario',

                'clientes',

                'clientesUsuario'

            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $usuario =
            User::findOrFail($id);


        $request->validate([

            'name' =>
            'required|string|max:255|unique:users,name,' . $id,

            'email' =>
            'required|email',

            'role' =>
            'required',

            'estado' =>
            'required',
            
            'password' =>
            'nullable|string|min:8|confirmed',

        ]);

        /*
        |--------------------------------------------------------------------------
        | DATOS DEL USUARIO
        |--------------------------------------------------------------------------
        */

        $usuario->name =

            $request->name;

        $usuario->email =
            $request->email;

        $usuario->role =
            $request->role;

        $usuario->estado =
            $request->estado;

            /*

            |--------------------------------------------------------------------------

            | CAMBIAR CONTRASEÑA

            |--------------------------------------------------------------------------

            */

            if ($request->filled('password')) {

                $usuario->password =

                    $request->password;

            }

        $usuario->save();


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR INVERSIONES
        |--------------------------------------------------------------------------
        */

        DB::table(
            'user_inversion'
        )
            ->where(
                'user_id',
                $usuario->id
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR PERMISOS DE MÓDULOS
        |--------------------------------------------------------------------------
        */

        DB::table(
            'user_inversion_modulos'
        )
            ->where(
                'user_id',
                $usuario->id
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR BUSINESS CUSTOMERS
        |--------------------------------------------------------------------------
        */

        DB::table(
            'user_business_customer'
        )
            ->where(
                'user_id',
                $usuario->id
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR ENTIDADES
        |--------------------------------------------------------------------------
        */

        DB::table(
            'user_entidad'
        )
            ->where(
                'user_id',
                $usuario->id
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR PERSONAS
        |--------------------------------------------------------------------------
        */

        DB::table(
            'user_cliente'
        )
            ->where(
                'user_id',
                $usuario->id
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | GUARDAR PERMISOS NUEVOS
        |--------------------------------------------------------------------------
        */

        if ($request->role == 'user') {

            /*
            |--------------------------------------------------------------------------
            | INVERSIONES
            |--------------------------------------------------------------------------
            */

            if ($request->has('inversiones')) {

                foreach (
                    $request->inversiones
                    as $inversionId
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | USUARIO - INVERSIÓN
                    |--------------------------------------------------------------------------
                    */

                    DB::table(
                        'user_inversion'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'inversion_id' =>
                        $inversionId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | PERMISOS DE MÓDULOS
                    |--------------------------------------------------------------------------
                    */

                    DB::table(
                        'user_inversion_modulos'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'inversion_id' =>
                        $inversionId,

                        'avaluos' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['avaluos']
                        ),

                        'activos' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['activos']
                        ),

                        'servicios' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['servicios']
                        ),

                        'comercial' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['comercial']
                        ),

                        'entidades' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['entidades']
                        ),

                        'estado_resultados' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['estado_resultados']
                        ),

                        'activos_registrales' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['activos_registrales']
                        ),

                        'bitacoras' =>
                        isset(
                            $request
                                ->permisos[$inversionId]['bitacoras']
                        ),

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | BUSINESS CUSTOMERS
            |--------------------------------------------------------------------------
            */

            if ($request->has('business_customers')) {

                foreach (
                    $request->business_customers
                    as $businessCustomerId
                ) {

                    DB::table(
                        'user_business_customer'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'business_customer_id' =>
                        $businessCustomerId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | ENTIDADES
            |--------------------------------------------------------------------------
            */

            if ($request->has('entidades')) {

                foreach (
                    $request->entidades
                    as $entidadId
                ) {

                    DB::table(
                        'user_entidad'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'entidad_id' =>
                        $entidadId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PERSONAS
            |--------------------------------------------------------------------------
            */

            if ($request->has('clientes')) {

                foreach (
                    $request->clientes
                    as $clienteId
                ) {

                    DB::table(
                        'user_cliente'
                    )->insert([

                        'user_id' =>
                        $usuario->id,

                        'cliente_id' =>
                        $clienteId,

                        'created_at' =>
                        now(),

                        'updated_at' =>
                        now(),

                    ]);
                }
            }
        }


        return redirect('/usuarios')
            ->with(
                'success',
                'Usuario actualizado correctamente'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESHABILITAR
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $usuario =
            User::findOrFail($id);


        $usuario->estado =
            false;

        $usuario->save();


        return redirect('/usuarios')
            ->with(
                'success',
                'Usuario deshabilitado'
            );
    }
}
