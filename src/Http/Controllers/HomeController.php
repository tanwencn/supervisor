<?php

namespace Laravel\Horizon\Http\Controllers;

use Laravel\Horizon\Horizon;
use Tanwencn\Supervisor\Supervisor;

class HomeController extends Controller
{
    /**
     * Single page application catch-all route.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('supervisor::layout', [
            //'horizonScriptVariables' => Horizon::scriptVariables(),
        ]);
    }

    /**
     * All resolvers
     * @return array
     */
    public function resolvers()
    {
        return Supervisor::resolvers();
    }

    public function directoris($resolver){
        Supervisor::resolver($resolver);
    }
}
