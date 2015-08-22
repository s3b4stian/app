<?php

namespace App\Controllers;

use Leviu\Routing\Controller;
use Leviu\Auth\Login;

class User extends Controller
{
    use \Leviu\Auth\ProtectTrait;
    
    public function __construct()
    {
        parent::__construct(__CLASS__);
        
        $this->model = $this->loadModel();
        
        $this->protectController(new Login());
        
        $this->view->data->isLogged = $this->isLogged;
        $this->view->data->userName = $this->login->userName;
    }
    
    public function index()
    {
        $this->view->data->users = $this->model->getAllUsers();
        
        $this->view->setTitle('App/Users');
        
        $this->view->addCss('css/user.css');
        $this->view->addJs('js/user.js');
        $this->view->addJs('js/dialog.js');
        $this->view->render('User/index');
    }
    
    public function enable($user_id)
    {
        $this->model->enable($user_id);
    }
    
    public function disable($user_id)
    {
        $this->model->disable($user_id);
    }
}