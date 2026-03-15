<?php

require_once APP_ROOT . '/app/controllers/Controller.php';

class AboutController extends Controller
{
    public function index(): string
    {
        $this->title = 'About';
        return $this->render('about/index');
    }
}
