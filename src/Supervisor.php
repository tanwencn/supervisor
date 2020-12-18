<?php

namespace Tanwencn\Supervisor;

use Closure;

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
     * Get the default JavaScript variables for Horizon.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'path' => config('horizon.path'),
        ];
    }

    public static function resolverViews()
    {
        return config('supervisor.view');
    }

    public static function resolever($name)
    {
        if(isset(static::$resolever[$name])) return static::$resolever[$name];

        $config = config("supervisor.resolvers.{$name}");
        if(empty($config[$name]))
            throw new \InvalidArgumentException("supervisor.resolvers.{$name} is not defind.");

        static::$resolever[$name] = new Resolever($config, config("supervisor.handler"));

        return static::resolever($name);

        $key = md5($class . $path);
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Driver [{$class}] is not supported.");
        } else if (!isset($this->analyses[$key]) || !$this->analyses[$key]) {
            $this->analyses[$key] = new $class($path, []);
        }
    }
