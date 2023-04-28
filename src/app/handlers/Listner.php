<?php

namespace listen;

use Phalcon\Acl\Enum;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Di\Injectable;

class Listner extends Injectable
{
    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dispatcher)
    {
        $aclFile = APP_PATH . '/security/acl.cache';
        if (true !== is_file($aclFile)) {
            $acl = new Memory();
            $acl->setDefaultAction(Enum::DENY);
            $acl->addRole('user');
            $acl->addRole('guest');
            $acl->addRole('admin');
            $acl->addRole('manager');
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
            $acl->deny("manager", "*", "*");

            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(
                file_get_contents($aclFile)
            );
        }
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
        if (true === $acl->isAllowed($role, $controller, $action)) {
            echo 'Access granted!';
        } else {
            echo 'Access denied :(';
            // die;
            $this->response->redirect('index/index');
            
        }
    }
}
