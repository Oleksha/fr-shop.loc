<?php

namespace PHPFramework;

class Application
{

    /**
     * Строка запроса
     * @var string
     */
    protected string $uri;
    /**
     * Объект запроса
     * @var Request
     */
    public Request $request;
    /**
     * Объект ответа
     * @var Response
     */
    public Response $response;
    /**
     * Объект маршрутизатора
     * @var Router
     */
    public Router $router;
    /**
     * Объект вида
     * @var View
     */
    public View $view;
    /**
     * Объект сессии
     * @var Session
     */
    public Session $session;
    /**
     * Объект Кеша
     * @var Cache
     */
    public Cache $cache;
    /**
     * Объект подключения к БД
     * @var Database
     */
    public Database $db;
    /**
     * Текущий класс Application
     * @var Application
     */
    public static Application $app;
    protected array $container = [];

    public function __construct()
    {
        self::$app = $this;
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->request = new Request($this->uri);
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View(LAYOUT);
        $this->session = new Session();
        $this->cache = new Cache();
        $this->generateCsrfToken();
        $this->db = new Database();
        Auth::setUser();
    }

    public function run(): void
    {
        echo $this->router->dispatch();
    }

    private function generateCsrfToken(): void
    {
        if (!session()->has('csrf_token')) {
            session()->set('csrf_token', md5(uniqid(mt_rand(), true)));
        }
    }

    public function get($key, $default = null)
    {
        return $this->container[$key] ?? $default;
    }

    public function set($key, $value): void
    {
        $this->container[$key] = $value;
    }
}