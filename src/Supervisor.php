<?php

namespace Tanwencn\Supervisor;

use Closure;
use Tanwencn\Supervisor\Mode\DatabaseMode;
use Tanwencn\Supervisor\Mode\FilesystemMode;
use Tanwencn\Supervisor\Mode\PositiveStreamMode;
use Tanwencn\Supervisor\Mode\ReverseStreamMode;

class Supervisor
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

    public static $resolever;

    /**
     * Determine if the given request can access the Horizon dashboard.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate Horizon users.
     *
     * @param \Closure $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Get the default JavaScript variables for Supervisor.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'path' => config('supervisor.path'),
        ];
    }

    public static function resolverViews()
    {
        return config('supervisor.view');
    }

    /**
     * @param string $name
     * @return Resolever
     */
    public static function resolever(string $name)
    {
        if (isset(static::$resolever[$name])) return static::$resolever[$name];

        $config = config("supervisor.resolvers.{$name}");

        if (empty($config))
            throw new \InvalidArgumentException("supervisor.resolvers.{$name} is not defind.");

        static::$resolever[$name] = new Resolever($config, array_merge([
            'filesystem' => FilesystemMode::class,
            'database' => DatabaseMode::class,
        ], config("supervisor.mode", [])), $name);

        return static::resolever($name);
    }

    public static function config($key)
    {
        return config("supervisor.{$key}");
    }

    public static function viewConfig()
    {
        $data = [];
        $config = config("supervisor.resolvers", []);
        foreach ($config as $key => $c){
            $data[$key]['render'] = data_get($c, 'render', []);
        }

        return $data;
    }
}
