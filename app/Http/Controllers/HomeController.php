<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Mostrar el dashboard de analytics
     */
    public function analytics(): View
    {
        return view('analytics');
    }

    /**
     * Mostrar el dashboard de votos del usuario
     */
    public function votos(): View
    {
        return view('votos');
    }

    /**
     * Mostrar el dashboard de administración de votos
     */
    public function adminVotos(): View
    {
        return view('admin.votos');
    }
}
