# Laravel Supervisor
## 介绍

Supervisor 为你的 Laravel 文件提供了一个美观的可视化列表和文件解析驱动的配置；默认提供了Laravel日志解析驱动，它可以方便的解析基于 Filesystem 的日志文件。

所有的配置存储在一个简单的配置文件中，你可以在整个团队都可以进行协作的地方进行源码控制。

## 特征

 - 视图使用Vue框架，交互友好

 - 可以解析超大文件，并在视图中提供搜索功能

 - 提供基于 Filesystem 的Laravel日志解析驱动

 - 可自定义解析驱动，解析任何格式文件并在视图中显示

## 安装

可以使用 Composer 将 Horization 安装到你的 Laravel 项目里：

```bash
composer require tanwencn/supervisor
```

```bash
php artisan supervisor:install
```

### 配置

Supervisor 资源发布之后，他的主要配置文件会被分配到 config/supervisor.php 文件。可以用这个配置文件配置工作选项。

### 访问授权

Supervisor 在 /supervisor 上显示了一个视图面板。默认情况下，你只能在 local 环境中访问这个面板。在你的 App/Providers/AppServiceProvider.php 文件中添加 gate 方法来控制着在非本地环境中对 Supervisor 的访问：

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



