<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigurationCatalog;
use App\Models\ConfigurationOption;
use App\Models\GoogleWorkspaceSetting;
use App\Models\TrustedDevice;
use Illuminate\Support\Facades\Schema;
use App\Models\InvoiceType;

class ConfigurationOptionController extends Controller
{
    /*
|--------------------------------------------------------------------------
| INDEX
|--------------------------------------------------------------------------
*/

public function index(Request $request)
{
    $catalogId = $request->catalog;


    /*
    |--------------------------------------------------------------------------
    | TODOS LOS CATÁLOGOS
    |--------------------------------------------------------------------------
    */

    $catalogs = ConfigurationCatalog::where('active', true)
        ->orderBy('name')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | CATÁLOGO SELECCIONADO
    |--------------------------------------------------------------------------
    */

    $catalogSelected = null;

    $options = collect();


    if ($catalogId) {

        $catalogSelected =
            ConfigurationCatalog::findOrFail($catalogId);


        $options =
            $catalogSelected->options()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

    }


    /*
    |--------------------------------------------------------------------------
    | GOOGLE WORKSPACE
    |--------------------------------------------------------------------------
    */

    $googleSetting = GoogleWorkspaceSetting::first();


    $googleClientId =
        $googleSetting?->client_id;


    $googleEmail =
        $googleSetting?->email;


    $googleConnected =
        $googleSetting?->active ?? false;


    $googleAccount =
        $googleSetting?->email;


    $googleConnectedAt =
        $googleSetting?->connected_at;

        $trustedDevices = TrustedDevice::with('user')
    ->orderByDesc('last_used_at')
    ->get();
    


    return view(
        'configurations.index',
        compact(
            'catalogs',
            'catalogSelected',
            'options',
            'googleClientId',
            'googleEmail',
            'googleConnected',
            'googleAccount',
            'googleConnectedAt',
            'trustedDevices'
        )
    );
}


    /*
    |--------------------------------------------------------------------------
    | CREAR CATÁLOGO
    |--------------------------------------------------------------------------
    */

    public function storeCatalog(Request $request)
    {
        $request->validate([

            'name' =>
                'required|string|max:255|unique:configuration_catalogs,name',

            'description' =>
                'nullable|string',

        ]);


        $catalog =
            ConfigurationCatalog::create([

                'name' =>
                    $request->name,

                'description' =>
                    $request->description,

                'active' =>
                    true,

            ]);


        return redirect(
            '/configuraciones?catalog=' . $catalog->id
        )
        ->with(
            'success',
            'Catálogo creado correctamente.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AGREGAR OPCIÓN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'catalog_id' =>
                'required|exists:configuration_catalogs,id',

            'name' =>
                'required|string|max:255',

            'description' =>
                'nullable|string',

        ]);


        ConfigurationOption::create([

            'catalog_id' =>
                $request->catalog_id,

            'name' =>
                $request->name,

            'description' =>
                $request->description,

            'active' =>
                true,

            'sort_order' =>
                0,

        ]);


        return redirect(
            '/configuraciones?catalog=' .
            $request->catalog_id
        )
        ->with(
            'success',
            'Opción agregada correctamente.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / DESACTIVAR OPCIÓN
    |--------------------------------------------------------------------------
    */

    public function toggle($id)
    {
        $option =
            ConfigurationOption::findOrFail($id);


        $option->active =
            !$option->active;


        $option->save();


        return redirect()->back()
            ->with(
                'success',
                'Estado actualizado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR OPCIÓN
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $option =
            ConfigurationOption::findOrFail($id);


        $catalogId =
            $option->catalog_id;


        $option->delete();


        return redirect(
            '/configuraciones?catalog=' .
            $catalogId
        )
        ->with(
            'success',
            'Opción eliminada correctamente.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR CATÁLOGO
    |--------------------------------------------------------------------------
    */

    public function destroyCatalog($id)
    {
        $catalog =
            ConfigurationCatalog::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Al eliminar el catálogo se eliminan sus opciones
        | gracias a cascadeOnDelete()
        |--------------------------------------------------------------------------
        */

        $catalog->delete();


        return redirect(
            '/configuraciones'
        )
        ->with(
            'success',
            'Catálogo eliminado correctamente.'
        );
    }
}