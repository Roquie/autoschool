<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Main extends Controller_Ajax_Ajax
{

    public function before()
    {
        parent::before();

        // мыло юзера сайта (проверять незя т.к. тут есть в LK_Ajax есть метод подачи доков)
        //$email = Cookie::get('userEmail'); if (!is_null($email)) HTTP::redirect('lk');

        $this->auto_render = false;
        if (Kohana::$environment === Kohana::PRODUCTION)
            if (!Request::initial()->is_ajax())
                throw new HTTP_Exception_404();

    }


}