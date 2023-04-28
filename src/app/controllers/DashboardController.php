<?php

use Phalcon\Mvc\Controller;

// defalut dashboard controller view

class DashboardController extends Controller
{
    public function indexAction()
    {
        if ($this->cookies->has("email")) {
            
            echo  $this->view->date = $this->time->dat();
            echo "<br>";
        } else {
            $this->response->redirect('login/index');
        }
    }
    public function logoutAction()
    {
        $this->session->destroy();
        $this->response->redirect('login/index');
    }
}
