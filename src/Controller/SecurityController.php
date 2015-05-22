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

    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function loginAction() {
        return $this->app['twig']->render('login.twig', array(
            'error'         => $this->app['security.last_error']($this->app['request']),
            'last_username' => $this->app['session']->get('_security.last_username'),
        ));
    }

}