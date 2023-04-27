<?php

namespace listen;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Di\Injectable;

class Listner extends Injectable
{
    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dispatcher)
    {

        $acl = new Memory();

        $acl->addRole('user');
        $acl->addRole('guest');
        $acl->addRole('admin');


        $acl->addComponent(
            'index',
            [
                'index',

            ]
        );
        $acl->addComponent(
            'login',
            [
                'login',
                'index'
            ]
        );
        $acl->addComponent(
            'signup',
            [
                'register',
                'index'

            ]
        );
        $acl->addComponent(
            'dashboard',
            [
                'logout',
                'index'

            ]
        );
        $action = "index";
        $controller = "index";
        $role = "guest";

        if (!empty($dispatcher->getActionName())) {
            $action =  $dispatcher->getActionName();
        }
        if (!empty($dispatcher->getControllerName())) {
            $controller =  $dispatcher->getControllerName();
        }
        if (!empty($app->request->get('role'))) {
            $role =  $app->request->get('role');
        }

        $acl->allow('user', 'index', 'index');
        $acl->allow("user", 'login', 'login');
        $acl->allow("user", 'login', 'index');
        $acl->allow("user", 'signup', 'index');
        $acl->allow("user", 'signup', 'register');
        $acl->allow('user', 'dashboard', 'index');
        $acl->allow('user', 'dashboard', 'logout');
        $acl->allow("guest", 'signup', 'register');
        $acl->allow("guest", 'signup', 'index');
        $acl->allow("guest", 'index', 'index');
        $acl->allow("admin", '*', '*');

        if (1 == $acl->isAllowed($role, $controller, $action)) {
            echo "Permission granted";
        } else {
            echo "Permission denied ";
            die;
        }
    }
}
