<?php

abstract class Controller
{
    public string $title = '';

    protected function render(string $view, array $vars = []): string
    {
        extract($vars);
        $__t = microtime(true);
        ob_start();
        require APP_ROOT . '/app/views/' . $view . '.php';
        $__output = ob_get_clean();
        if (defined('DEV_LOGGING') && DEV_LOGGING) {
            DevLog::render($view, (microtime(true) - $__t) * 1000);
        }
        return $__output;
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
