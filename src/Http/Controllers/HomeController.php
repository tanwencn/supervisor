<?php

namespace Tanwencn\Supervisor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            'supervisorBasePath' => Supervisor::config('deep_base_router').'/'.Supervisor::config('path')
        ]);
    }

    /**
     * All resolvers
     * @return array
     */
    public function resolvers()
    {
        return Supervisor::resolverViews();
    }

    public function directoris(Request $request)
    {
        $request->validate([
            'resolver' => 'required'
        ]);

        $path = base64_decode($request->query('code')) ?: '/';

        return Supervisor::resolever($request->query('resolver'))->files($path);
    }

    public function contents(Request $request)
    {
        $input = $request->validate([
            'resolver' => 'required',
            'code' => 'required',
            'reset' => 'nullable|in:0,1'
        ]);

        if(!empty($input['reset']))
            Cache::forget("supervisor-resolever-{$input['code']}");

        $path = base64_decode($input['code']);

        $resolever = Cache::get("supervisor-resolever-{$input['code']}");

        if (!$resolever)
            $resolever = Supervisor::resolever($input['resolver']);

        $container = $resolever->container($path);

        $start_time = time();

        $single_time = Supervisor::config('single_time');

        $data = [];
        do {
            $next = $container->next();
            $data[] = $next;
            $use_time = time() - $start_time;
        } while ($use_time < $single_time && !empty($next));

        Cache::put("supervisor-resolever-{$input['code']}", $resolever, 300);

        return array_filter($data);
    }
}
