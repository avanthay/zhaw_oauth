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

    public function adminAction(Application $app) {
        return $app['twig']->render('admin.twig', array(
            'user' => $app['security']->getToken()->getUser()
        ));
    }

}