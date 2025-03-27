<?php

use PHPFramework\Application;
use PHPFramework\Cache;
use PHPFramework\Database;
use PHPFramework\Request;
use PHPFramework\Response;
use PHPFramework\Session;
use PHPFramework\View;
use PHPMailer\PHPMailer\PHPMailer;

function app(): Application
{
    return Application::$app;
}

/**
 * Получаем объект класса Request
 * @return Request
 */
function request(): Request
{
    return app()->request;
}

/**
 * Получаем объект класса Request
 * @return Response
 */
function response(): Response
{
    return app()->response;
}

/**
 * Получаем объект класса Session
 * @return Session
 */
function session(): Session
{
    return app()->session;
}

/**
 * Получаем объект класса Cache
 * @return Cache
 */
function cache(): Cache
{
    return app()->cache;
}

function router(): \PHPFramework\Router
{
    return app()->router;
}

function get_route_params(): array
{
    return router()->getRouteParams();
}

function get_route_param($key, $default = ''): string
{
    $params = router()->getRouteParams();
    return $params[$key] ?? $default;
}

function array_value_search($array, $index, $value): int|string|null
{
    foreach ($array as $key => $val) {
        if ($val[$index] == $value) {
            return $key;
        }
    }
    return null;
}

function db(): Database
{
    return app()->db;
}

/**
 * Подключает вид или возвращает экземпляр класса
 * @param $view
 * @param $data
 * @param $layout
 * @return string|View
 */
function view($view = '', $data = [], $layout = ''): string|View
{
    if ($view) {
        return app()->view->render($view, $data, $layout);
    }
    return app()->view;
}

/**
 * Прекращает работу и выводи ошибку
 * @param string $error
 * @param int $code
 * @return void
 */
function abort(string $error = '', int $code = 404)
{
    response()->setResponseCode($code);
    echo view("errors/$code", ['error' => $error], false);
    die;
}

function redirect($url = ''): void
{
    response()->redirect($url);
}

function base_url($path = ''): string
{
    return PATH . $path;
}

function base_href($path = ''): string
{
    if (app()->get('lang')['base'] != 1) {
        return PATH . '/' . app()->get('lang')['code'] . $path;
    }
    return PATH . $path;
}

function uri_without_lang(): string
{
    $request_uri = request()->uri;
    $request_uri = explode('/', $request_uri, 2);
    if (array_key_exists($request_uri[0], LANGS)) {
        unset($request_uri[0]);
    }
    $request_uri = implode('/', $request_uri);
    return $request_uri ? '/' . $request_uri : '';
}

/**
 * Вывод Flash-сообщений
 * @return void
 */
function get_alerts(): void
{
    if (!empty($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $key => $value) {
            echo view()->renderPartial("incs/alert_$key", ["flash_$key" => session()->getFlash($key)]);
        }
    }
}

function get_errors($field_name): string
{
    $output = '';
    $errors = session()->get('form_errors');
    if (isset($errors[$field_name])) {
        $output .= '<div class="invalid-feedback d-block"><ul class="list-unstyled">';
        foreach ($errors[$field_name] as $error) {
            $output .= "<li>$error</li>";
        }
        $output .= '</ul></div>';
    }
    return $output;
}

function get_validation_class($field_name): string
{
    $errors = session()->get('form_errors');
    if (empty($errors)) {
        return '';
    }
    return isset($errors[$field_name]) ? 'is-invalid' : 'is-valid';
}

function old($field_name): string
{
    return isset(session()->get('form_data')[$field_name]) ? h(session()->get('form_data')[$field_name]) : '';
}

function h($str): string
{
    return htmlspecialchars($str, ENT_QUOTES);
}

function get_csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . session()->get('csrf_token') . '">';
}

function get_csrf_meta(): string
{
    return '<meta name="csrf_token" content="' . session()->get('csrf_token') . '">';
}

function check_auth(): bool
{
    return \PHPFramework\Auth::isAuth();
}

function get_user()
{
    return \PHPFramework\Auth::user();
}

function _e($key): void
{
    echo \PHPFramework\Language::get($key);
}

function __($key): string
{
    return \PHPFramework\Language::get($key);
}

function send_mail(array $to, string $subject, string $tpl, array $data = [], array $attachments = []): bool
{
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = MAIL_SETTINGS['debug'];
        $mail->isSMTP();
        $mail->Host = MAIL_SETTINGS['host'];
        $mail->SMTPAuth = MAIL_SETTINGS['auth'];
        $mail->Username = MAIL_SETTINGS['username'];
        $mail->Password = MAIL_SETTINGS['password'];
        $mail->SMTPSecure = MAIL_SETTINGS['secure'];
        $mail->Port = MAIL_SETTINGS['port'];
        // Получатели
        $mail->setFrom(MAIL_SETTINGS['from_email'], MAIL_SETTINGS['from_name']);
        foreach ($to as $email) {
            $mail->addAddress($email);
        }
        // Attachments
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        // Содержание
        $mail->isHTML(MAIL_SETTINGS['is_html']);
        $mail->CharSet = MAIL_SETTINGS['charset'];
        $mail->Subject = $subject;
        $mail->Body = view($tpl, $data, false);
        return $mail->send();
    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Ошибка: {$e->getMessage()}" . PHP_EOL . "Файл: {$e->getFile()}" . PHP_EOL . "Строка: {$e->getLine()}" . PHP_EOL . "---------------------------------------------" . PHP_EOL, 3, ERROR_LOGS);
        return false;
    }
}
