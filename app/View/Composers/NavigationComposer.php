<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;

class NavigationComposer
{
    /**
     * Vincula los datos a la vista.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $sesionAbierta = null;

        // Solo consultamos si hay un usuario autenticado
        if (Auth::check()) {
            $sesionAbierta = SesionCaja::with('caja')
                ->where('user_id', Auth::id())
                ->whereNull('saldo_final_declarado')
                ->first();
        }

        // Pasamos la variable a la vista
        $view->with('sesionAbierta', $sesionAbierta);
    }
}