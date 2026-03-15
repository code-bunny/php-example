<?php

abstract class Controller
{
    public string $title = '';

    protected function render(string $view, array $vars = []): string
    {
        extract($vars);
        ob_start();
        require APP_ROOT . '/app/views/' . $view . '.php';
        return ob_get_clean();
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
