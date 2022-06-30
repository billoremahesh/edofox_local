<?php

namespace App\Controllers;


class Errors extends BaseController
{

    public function index()
    {
    }

    public function show404()
    {
        echo view('custom_errors/page_not_found');
    }
}
