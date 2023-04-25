<?php

use Phalcon\Mvc\Controller;

// defalut controller view
class IndexController extends Controller
{
    public function indexAction()
    {
       
   $ob= new \app\Assets\sample ();
   $ob->demo();
        
    }
}
