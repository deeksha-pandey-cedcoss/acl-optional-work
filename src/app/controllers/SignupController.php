<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{

    public function IndexAction()
    {
        // defalut action


    }

    public function registerAction()
    {
        $user = new Users();

        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'email',
                'password',

            ]
        );
        $success = $user->save();
        $this->session->set('name', $user->name);
        $this->session->set('email', $user->email);
        $this->session->set('password', $user->password);

        if (isset($_POST['remember']) == "on") {
            $this->cookies->set('email', $this->session->get('email'), time() + 15 * 86400);
            $this->cookies->set('pass', $this->session->get('password'), time() + 15 * 86400);
        } else {
            $this->cookies->set('email', $this->session->get('email'), time() - 15 * 86400);
            $this->cookies->set('pass', $this->session->get('password'), time() - 15 * 86400);
        }

        $this->view->success = $success;
        if ($success) {
            $this->view->message = "Register succesfully";
        } else {
            $this->view->message = "Not Register due to following reason: <br>" . implode("<br>", $user->getMessages());
        }
        $this->response->redirect('login/index');
    }
}
