<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ActivoRegistralController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InversionController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AvaluoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ComercialController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstadoResultadoController;
use App\Http\Controllers\BusinessCustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ConfigurationOptionController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\GoogleWorkspaceController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\SujetoController;
use App\Http\Controllers\MovimientoController;

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | CLIENTES
    |--------------------------------------------------------------------------
    */

/*
|--------------------------------------------------------------------------
| CLIENTES (SOLO ADMIN)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| CLIENTES / PERSONAS
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| LISTADO — USUARIOS AUTENTICADOS
|--------------------------------------------------------------------------
*/

Route::get(
    '/clientes',
    [ClienteController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN — SOLO ADMIN
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| CLIENTES / PERSONAS
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| LISTADO — USUARIOS AUTENTICADOS
|--------------------------------------------------------------------------
*/

Route::get(
    '/clientes',
    [ClienteController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| VER / EDITAR — USUARIOS AUTENTICADOS
|--------------------------------------------------------------------------
*/

Route::get(
    '/clientes/{id}/edit',
    [ClienteController::class, 'edit']
);


/*
|--------------------------------------------------------------------------
| CREAR / MODIFICAR — SOLO ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->group(function () {

    Route::get(
        '/clientes/create',
        [ClienteController::class, 'create']
    );

    Route::post(
        '/clientes',
        [ClienteController::class, 'store']
    );

    Route::put(
        '/clientes/{id}',
        [ClienteController::class, 'update']
    );

    Route::delete(
        '/clientes/{id}',
        [ClienteController::class, 'destroy']
    );



});
    /*
    |--------------------------------------------------------------------------
    | INVERSIONES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inversiones',
        [InversionController::class, 'index']
    );

    Route::get(
        '/inversiones/create',
        [InversionController::class, 'create']
    );

    Route::post(
        '/inversiones',
        [InversionController::class, 'store']
    );

    Route::get(
        '/inversiones/{id}/edit',
        [InversionController::class, 'edit']
    );

    Route::put(
        '/inversiones/{id}',
        [InversionController::class, 'update']
    );

    Route::delete(
        '/inversiones/{id}',
        [InversionController::class, 'destroy']
    );

    Route::get(
        '/inversiones/{id}',
        [InversionController::class, 'show']
    );
    /*
    |--------------------------------------------------------------------------
    | ACTIVOS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inversiones/{investment_id}/assets',
        [AssetController::class, 'index']
    );

    Route::get(
        '/inversiones/{investment_id}/assets/create',
        [AssetController::class, 'create']
    );

    Route::post(
        '/inversiones/{investment_id}/assets',
        [AssetController::class, 'store']
    );

    Route::get(
        '/inversiones/{investment_id}/assets/{id}/edit',
        [AssetController::class, 'edit']
    );

    Route::put(
        '/inversiones/{investment_id}/assets/{id}',
        [AssetController::class, 'update']
    );

    Route::delete(
        '/inversiones/{investment_id}/assets/{id}',
        [AssetController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | AVALÚOS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inversiones/{inversion_id}/avaluos',
        [AvaluoController::class, 'index']
    );

    Route::get(
        '/inversiones/{inversion_id}/avaluos/create',
        [AvaluoController::class, 'create']
    );

    Route::post(
        '/inversiones/{inversion_id}/avaluos',
        [AvaluoController::class, 'store']
    );

    Route::get(
        '/inversiones/{inversion_id}/avaluos/{id}/edit',
        [AvaluoController::class, 'edit']
    );

    Route::put(
        '/inversiones/{inversion_id}/avaluos/{id}',
        [AvaluoController::class, 'update']
    );

    Route::delete(
        '/inversiones/{inversion_id}/avaluos/{id}',
        [AvaluoController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | SERVICIOS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inversiones/{inversion_id}/servicios',
        [ServicioController::class, 'index']
    );

    Route::get(
        '/inversiones/{inversion_id}/servicios/create',
        [ServicioController::class, 'create']
    );

    Route::post(
        '/inversiones/{inversion_id}/servicios',
        [ServicioController::class, 'store']
    );

    Route::get(
        '/inversiones/{inversion_id}/servicios/{id}/edit',
        [ServicioController::class, 'edit']
    );

    Route::put(
        '/inversiones/{inversion_id}/servicios/{id}',
        [ServicioController::class, 'update']
    );

    Route::delete(
        '/inversiones/{inversion_id}/servicios/{id}',
        [ServicioController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | COMERCIAL
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inversiones/{inversion_id}/comercial',
        [ComercialController::class, 'index']
    );

    Route::get(
        '/inversiones/{inversion_id}/comercial/create',
        [ComercialController::class, 'create']
    );

    Route::post(
        '/inversiones/{inversion_id}/comercial',
        [ComercialController::class, 'store']
    );

    Route::get(
        '/inversiones/{inversion_id}/comercial/{id}/edit',
        [ComercialController::class, 'edit']
    );

    Route::put(
        '/inversiones/{inversion_id}/comercial/{id}',
        [ComercialController::class, 'update']
    );

    Route::delete(
        '/inversiones/{inversion_id}/comercial/{id}',
        [ComercialController::class, 'destroy']
    );

    /*
|--------------------------------------------------------------------------
| ENTIDADES (SOLO ADMIN)
|--------------------------------------------------------------------------
*/

    /*
|--------------------------------------------------------------------------
| ENTIDADES
|--------------------------------------------------------------------------
*/

    Route::get('/entidades', [EntidadController::class, 'index']);

    Route::get('/entidades/create', [EntidadController::class, 'create']);

    Route::post('/entidades', [EntidadController::class, 'store']);

    Route::get('/entidades/{id}/edit', [EntidadController::class, 'edit']);

    Route::put('/entidades/{id}', [EntidadController::class, 'update']);

    Route::delete('/entidades/{id}', [EntidadController::class, 'destroy']);

    Route::get(
        '/inversiones/{id}/entidades',
        [EntidadController::class, 'porInversion']
    );

    /*
|--------------------------------------------------------------------------
| ENTIDADES POR INVERSIÓN
|--------------------------------------------------------------------------
*/

    Route::get(
        '/inversiones/{id}/entidades',
        [EntidadController::class, 'porInversion']
    );

    /*
    |--------------------------------------------------------------------------
    | ACTIVOS REGISTRALES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/inversiones/{inversion}/activos-registrales',
        [ActivoRegistralController::class, 'index']
    );

    Route::get(
        '/inversiones/{inversion}/activos-registrales/create',
        [ActivoRegistralController::class, 'create']
    );

    Route::post(
        '/inversiones/{inversion}/activos-registrales',
        [ActivoRegistralController::class, 'store']
    );

    Route::get(
        '/activos-registrales/{id}/edit',
        [ActivoRegistralController::class, 'edit']
    );

    Route::put(
        '/activos-registrales/{id}',
        [ActivoRegistralController::class, 'update']
    );

    Route::delete(
        '/activos-registrales/{id}',
        [ActivoRegistralController::class, 'destroy']
    );


                /*
            |--------------------------------------------------------------------------
            | USUARIOS
            |--------------------------------------------------------------------------
            */

/*
|--------------------------------------------------------------------------
| USUARIOS (SOLO ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->group(function () {

    Route::get(
        '/usuarios',
        [UserController::class, 'index']
    );

    Route::get(
        '/usuarios/create',
        [UserController::class, 'create']
    );

    Route::post(
        '/usuarios',
        [UserController::class, 'store']
    );

    Route::get(
        '/usuarios/{id}/edit',
        [UserController::class, 'edit']
    );

    Route::put(
        '/usuarios/{id}',
        [UserController::class, 'update']
    );

});



                        /*
                    |--------------------------------------------------------------------------
                    | Estado de Resultados
                    |--------------------------------------------------------------------------
                    */


    Route::get('/inversiones/{inversion_id}/estado-resultados', [EstadoResultadoController::class, 'index']);

    Route::get('/inversiones/{inversion_id}/estado-resultados/create', [EstadoResultadoController::class, 'create']);

    Route::post('/inversiones/{inversion_id}/estado-resultados', [EstadoResultadoController::class, 'store']);

    Route::get('/inversiones/{inversion_id}/estado-resultados/{id}/edit', [EstadoResultadoController::class, 'edit']);

    Route::put('/inversiones/{inversion_id}/estado-resultados/{id}', [EstadoResultadoController::class, 'update']);

    Route::delete('/inversiones/{inversion_id}/estado-resultados/{id}', [EstadoResultadoController::class, 'destroy']);

    Route::post(
        '/inversiones/{inversion_id}/estado-resultados/generar',
        [EstadoResultadoController::class, 'generar']
    );



    Route::post(
        '/documentos',
        [DocumentController::class, 'store']
    );

    Route::delete(
        '/documentos/{id}',
        [DocumentController::class, 'destroy']
    );


    Route::delete('/inversiones/{inversion}', [InversionController::class, 'destroy']);



/*
|--------------------------------------------------------------------------
| CONFIGURACIONES
|--------------------------------------------------------------------------
*/

Route::get(
    '/configuraciones',
    [ConfigurationOptionController::class, 'index']
)->name('configuraciones');

Route::post(
    '/configuraciones',
    [ConfigurationOptionController::class, 'store']
);

Route::put(
    '/configuraciones/{id}',
    [ConfigurationOptionController::class, 'update']
);

Route::patch(
    '/configuraciones/{id}/toggle',
    [ConfigurationOptionController::class, 'toggle']
);

Route::delete(
    '/configuraciones/{id}',
    [ConfigurationOptionController::class, 'destroy']
);


Route::get(
    '/configuraciones/create',
    [ConfigurationOptionController::class, 'create']
);

Route::post(
    '/configuraciones/catalogos',
    [ConfigurationOptionController::class, 'storeCatalog']
);


Route::post(
    '/inversiones/{investment_id}/assets/{id}/duplicate',
    [AssetController::class, 'duplicate']
);


/*
|--------------------------------------------------------------------------
| ALERTAS
|--------------------------------------------------------------------------
*/

Route::post(
    '/alerts',
    [AlertController::class, 'store']
)->name('alerts.store');

Route::patch('/alerts/{alert}/toggle', [AlertController::class, 'toggle'])
    ->name('alerts.toggle');

Route::delete(
    '/alerts/{alert}',
    [AlertController::class, 'destroy']
)->name('alerts.destroy');


Route::post(
    '/configuraciones/google',
    [GoogleWorkspaceController::class, 'save']
)->name('google.save');

Route::get(
    '/configuraciones/google/connect',
    [GoogleWorkspaceController::class, 'connect']
)->name('google.connect');

Route::get(
    '/configuraciones/google/callback',
    [GoogleWorkspaceController::class, 'callback']
)->name('google.callback');

Route::post(
    '/configuraciones/google/disconnect',
    [GoogleWorkspaceController::class, 'disconnect']
)->name('google.disconnect');




Route::get(
    '/inversiones/{inversion}/bitacoras',
    [BitacoraController::class, 'index']
)->name('inversiones.bitacoras');




Route::get(
    '/business-customers',
    [BusinessCustomerController::class, 'index']
);

Route::get(
    '/business-customers/create',
    [BusinessCustomerController::class, 'create']
);

Route::post(
    '/business-customers',
    [BusinessCustomerController::class, 'store']
);

Route::get(
    '/business-customers/{id}/edit',
    [BusinessCustomerController::class, 'edit']
);

Route::put(
    '/business-customers/{id}',
    [BusinessCustomerController::class, 'update']
);

Route::delete(
    '/business-customers/{id}',
    [BusinessCustomerController::class, 'destroy']
);


Route::post(
    '/notes',
    [NoteController::class, 'store']
);

Route::delete(
    '/notes/{id}',
    [NoteController::class, 'destroy']
);



/*
|--------------------------------------------------------------------------
|Imagenes
|--------------------------------------------------------------------------
*/

Route::post('/imagenes', [ImagenController::class, 'store'])
    ->name('imagenes.store');

Route::delete('/imagenes/{imagen}', [ImagenController::class, 'destroy'])
    ->name('imagenes.destroy');


Route::resource('expedientes', ExpedienteController::class);

Route::post(
    '/expedientes/{expediente}/sujetos',
    [SujetoController::class, 'store']
)->name('expedientes.sujetos.store');

Route::put(
    '/sujetos/{sujeto}',
    [SujetoController::class, 'update']
)->name('sujetos.update');

Route::delete(
    '/sujetos/{sujeto}',
    [SujetoController::class, 'destroy']
)->name('sujetos.destroy');

Route::post(
    '/expedientes/{expediente}/movimientos',
    [MovimientoController::class, 'store']
)->name('expedientes.movimientos.store');

Route::put(
    '/movimientos/{movimiento}',
    [MovimientoController::class, 'update']
)->name('movimientos.update');

Route::delete(
    '/movimientos/{movimiento}',
    [MovimientoController::class, 'destroy']
)->name('movimientos.destroy');

Route::patch(
    '/expedientes/{expediente}/estado',
    [ExpedienteController::class, 'updateEstado']
)->name('expedientes.estado.update');

Route::put('/movimientos/{movimiento}', [MovimientoController::class, 'update'])
    ->name('movimientos.update');

});