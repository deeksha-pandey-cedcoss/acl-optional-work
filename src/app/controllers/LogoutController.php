<?php

use Phalcon\Mvc\Controller;
// defalut controller view
class LogoutController extends Controller
{
    public function indexAction()
    {
        // version name and host name of configuration

        $this->session->destroy();


        if ($this->cookies->has("email") && $this->cookies->has("pass")) {
            $this->cookies->set('email', $this->session->get('email'), time() - 15 * 86400);
            $this->cookies->set('pass', $this->session->get('password'), time() - 15 * 86400);
        }

        $this->response->redirect('signup/index');
    }
}
