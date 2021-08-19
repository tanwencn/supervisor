# Laravel Supervisor
## 介绍

Supervisor 为你的 Laravel 文件提供了一个美观的可视化列表， 可以方便的显示出基于Filesystem和Db的数据。

对于Filesystem模型，默认提供了```Laravel日志```、```Json```、```正则表达式```解析器以解析不同需求的文件内容。

所有的配置存储在一个简单的配置文件中，你可以方便的对其进行源码控制。

![image](https://user-images.githubusercontent.com/12136184/130049625-74e631d1-02c7-4669-b2a5-8e34bee1a7a9.png)

## 安装

可以使用 Composer 将 Horization 安装到你的 Laravel 项目里：

```bash
composer require tanwencn/supervisor
```

```bash
php artisan supervisor:install
```

```Supervisor```的默认配置显示项为```filesystem.logs```。所以还需要在 ```config/filesystems.php``` 中添加 ```disks``` ：
```php
'logs' => [
            'driver' => 'local',
            'root' => storage_path('logs'),
        ]
```

### 配置

```Supervisor``` 资源发布之后，他的主要配置文件会被分配到 ```config/supervisor.php``` 文件。可以用这个配置文件配置工作选项。

在配置文件中，```resolvers```项默认提供了Laravel日志解析配置，还有```Json```、```正则表达式```、```mysql```解析配置示例，你只要稍微对其进行改动，就可以直接用在你的应用中了。

注意```resolvers```只是解析配置项，要在视图中显示，需要把其添加进```view```项中。

### 访问授权

```Supervisor``` 在 ```/supervisor``` 路径上显示了一个视图面板。默认情况下，你只能在 ```local``` 环境中访问这个面板。在你的 ```App/Providers/AppServiceProvider.php``` 文件中添加 ```gate``` 方法来控制着在非本地环境中对 ```Supervisor``` 的访问：

```php
public function boot()
{
    $this->gateSupervisor();
}

protected function gateSupervisor()
{
    Gate::define('viewSupervisor', function ($user) {
        return in_array($user->name, [
            'tanwencn',
        ]);
    });
}
```



