<?php

class Controller_Admin extends Controller_Common
{

    public function __construct()
    {
        parent::__construct();

        $this->check_access();


    }

    public function index()
    {
        $this->display('admin_uploadgen.dwt');

    }
}


