<p align="center">
  <a href="https://www.meanify.co?from=github&lib=laravel-payment-hub">
    <img src="https://meanify.co/assets/core/img/logo/png/meanify_color_dark_horizontal_02.png" width="200" alt="Meanify Logo" />
  </a>
</p>


# Laravel Activity Log

Este pacote oferece uma solução robusta para registrar logs de atividades (CRUD) e requisições HTTP em aplicações Laravel. Ideal para auditoria, rastreamento de mudanças e análise de histórico.

---

## 🚀 Instalação (Desenvolvimento Local com Link Simbólico)

1. Adicione no `composer.json` do seu projeto principal:

```json
"repositories": [
  {
    "type": "path",
    "url": "../packages/laravel-activity-log",
    "options": {
      "symlink": true
    }
  }
],
"minimum-stability": "dev",
"prefer-stable": true
```

2. Instale o pacote:

```bash
composer require meanify-co/laravel-activity-log:@dev
```

---

## ⚙️ Configuração

1. Publique o config:

```bash
php artisan vendor:publish --tag=config --provider="Meanify\LaravelActivityLog\ActivityLogServiceProvider"
```

2. Execute as migrations:

```bash
php artisan migrate
```

3. Ative o middleware no `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        \Meanify\LaravelActivityLog\Middleware\MeanifyLaravelActivityLogRequestInterceptor::class,
    ],
];
```

---

## 🔁 Registro do Observer (via AbstractModel)

No seu `AbstractModel`:

```php
protected static function booted(): void
{
    static::observe(\Meanify\LaravelActivityLog\Observers\CrudObserver::class);
}
```

E nas suas models, estenda dele:

```php
class User extends AbstractModel
```

---

## ⚙️ Comportamentos configuráveis por model

Você pode adicionar nas models:

```php
public static bool   $meanify_log_enabled = true;
public static array  $meanify_log_ignore_changes = ['updated_at'];
public static array  $meanify_log_ignore_actions = ['restored'];
public static bool   $meanify_log_ignore_system = true;
public static array  $meanify_log_ignore_system_except = ['status'];

public static function meanifyLogIgnoreSystemCondition(array $changes): bool
{
    return isset($changes['status']) && $changes['status'] === 'confirmed';
}

public static function meanifyLogHeaders(): array
{
    return [
        'user_id' => auth()->id(),
        'account_id' => auth()->user()?->account_id,
        'request_uuid' => request()->header('x-request-id'),
        'ip_address' => request()->ip(),
    ];
}
```

---

## 🧩 Comandos Artisan

```bash
php artisan meanify:activity-log:list
php artisan meanify:activity-log:prune
php artisan meanify:activity-log:test
php artisan meanify:activity-log:stats
```

---

## 📦 Produção (Packagist)

Após publicação, remova o `repositories` do composer.json e rode:

```bash
composer remove meanify-co/laravel-activity-log
composer require meanify-co/laravel-activity-log
```

---

Feito com ❤️ pela Meanify.