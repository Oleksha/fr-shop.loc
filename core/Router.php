<?php

namespace PHPFramework;

class Router
{
    /**
     * Таблица маршрутов
     * @var array
     */
    protected array $routes = [];
    /**
     * Параметры маршрута
     * @var array
     */
    protected array $route_params = [];

    public function __construct(
        protected Request  $request,
        protected Response $response
    )
    {
    }

    public function add($path, $callback, $method): self
    {
        $path = trim($path, '/');
        if (is_array($method)) {
            $method = array_map('strtoupper', $method);
        } else {
            $method = [strtoupper($method)];
        }
        $this->routes[] = [
            'path' => "/$path",
            'callback' => $callback,
            'middleware' => [],
            'method' => $method,
            'needCsrfToken' => true,
        ];
        return $this;
    }

    public function get($path, $callback): self
    {
        return $this->add($path, $callback, 'GET');
    }

    public function post($path, $callback): self
    {
        return $this->add($path, $callback, 'POST');
    }

    public function put($path, $callback): self
    {
        return $this->add($path, $callback, 'PUT');
    }

    /**
     * Возвращает таблицу маршрутов
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function dispatch(): mixed
    {
        $path = $this->request->getPath();
        $route = $this->matchRoute($path);
        if (false === $route) {
            abort();
        }
        if (is_array($route['callback'])) {
            $route['callback'][0] = new $route['callback'][0];
        }
        return call_user_func($route['callback']);
    }

    protected function matchRoute($path): mixed
    {
        $allowed_methods = [];
        foreach ($this->routes as $route) {
            if (MULTILANGS) {
                $pattern = "#^/?(?P<lang>[a-z]+)?{$route['path']}?$#";
            } else {
                $pattern = "#^{$route['path']}$#";
            }
            if (
                preg_match($pattern, "/$path", $matches)
                //&&
                //in_array($this->request->getMethod(), $route['method'])
            ) {
                if (!in_array($this->request->getMethod(), $route['method'])) {
                    $allowed_methods =array_merge($allowed_methods, $route['method']);
                    continue;
                }
                /*if (!in_array($this->request->getMethod(), $route['method'])) {
                    if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
                        response()->json(['status' => 'error', 'answer' => 'Method not allowed'], 405);
                    }
                    abort('Метод не поддерживается', 405);
                }*/
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $this->route_params[$key] = $value;
                    }
                }
                // если язык есть, но его нет в массиве допустимых - 404
                // если язык есть, и он базовый - 404
                $lang = trim(get_route_param('lang'), '/');
                $base_lang =array_value_search(LANGS, 'base', 1);
                if (($lang && !array_key_exists($lang, LANGS)) || $lang == $base_lang) {
                    abort();
                }
                $lang = $lang ?: $base_lang;
                app()->set('lang', LANGS[$lang]);
                Language::load($route['callback']);
                if (request()->isPost()) {
                    if ($route['needCsrfToken'] && !$this->checkCsrfToken()) {
                        if (request()->isAjax()) {
                            // Если Ajax-запрос возвращаем json-ответ
                            echo json_encode([
                                'status' => 'error',
                                'data' => 'Ошибка безопасности',
                            ]);
                            die;
                        } else {
                            abort('Срок действия страницы истек', 419);
                        }
                    }
                }
                if ($route['middleware']) {
                    foreach ($route['middleware'] as $item) {
                        $middleware = MIDDLEWARE[$item] ?? false;
                        if ($middleware) {
                            (new $middleware)->handle();
                        }
                    }
                }
                return $route;
            }
        }
        if ($allowed_methods) {
            header("Allow: " . implode(', ', array_unique($allowed_methods)));
            if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
                response()->json(['status' => 'error', 'answer' => 'Method not allowed'], 405);
            }
            abort('Метод не поддерживается', 405);
        }
        return false;
    }

    /**
     * Убирает маршрут из проверки на наличие csrf-токена
     * @return $this
     */
    public function withoutCsrfToken(): self
    {
        $this->routes[array_key_last($this->routes)]['needCsrfToken'] = false;
        return $this;
    }

    /**
     * Проверка наличия и совпадения csrf-токена
     * @return bool
     */
    public function checkCsrfToken(): bool
    {
        return request()->post('csrf_token') && (request()->post('csrf_token') == session()->get('csrf_token'));
    }

    public function middleware(array $middleware): self
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $middleware;
        return $this;
    }

    public function getRouteParams(): array
    {
        return $this->route_params;
    }
}