<?php
/**
 * Class SecurityController
 * @package Dave\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


namespace Dave\Controller;


use Silex\Application;

class SecurityController {

    public function loginAction(Application $app) {
        return $app['twig']->render('login.twig', array(
            'error'         => $app['security.last_error']($app['request']),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

}