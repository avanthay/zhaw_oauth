<?php
/**
 * Class AdminController
 * @package Dave\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


namespace Dave\Controller;


use Silex\Application;

class AdminController {

    private $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    public function adminAction() {
        return $this->app['twig']->render('admin.twig', array(
            'user' => $this->app['security']->getToken()->getUser()
        ));
    }

}