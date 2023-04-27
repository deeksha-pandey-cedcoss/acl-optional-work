<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
// login controller
class LoginController extends Controller
{
    public function indexAction()
    {
        // default login view
    }
    // login action page
    public function loginAction()
    {
        if ($this->request->isPost()) {
            $password = $this->request->getPost("password");
            $email = $this->request->getPost("email");
        }
        if ($this->cookies->has("email")) {
            $mail = $this->cookies->get("email");
            $pass = $this->cookies->get("pass");
            $value_mail = $mail->getValue();
            $value_pass = $pass->getValue();
        }

        $success = Users::findFirst(array("email = ?0", "bind" => array($email)));

        if ($success) {
            $this->view->message = "LOGIN SUCCESSFULLY";
            $this->response->redirect('dashboard/index');
        } else {
            $response = new Response();

            $response->setStatusCode(403, 'Not Found');
            $response->setContent("Sorry, Credentials does not match");
            $response->send();
            $this->view->message = "Not Login succesfully ";
        }
    }
}
