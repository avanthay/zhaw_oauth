<?php


namespace Dave;

use Dave\Libraries\OAuth2Server\AccessTokenStorage;
use Dave\Libraries\OAuth2Server\AuthCodeStorage;
use Dave\Libraries\OAuth2Server\ClientStorage;
use Dave\Libraries\OAuth2Server\ScopeStorage;
use Dave\Libraries\OAuth2Server\SessionStorage;
use Dave\Provider\ControllerProvider;
use Dave\Provider\ServiceProvider;
use Dave\Provider\UserProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\ResourceServer;
use Silex\Application;


/**
 * Class App
 * @package Dave
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class App extends Application {

    public function __construct(array $values = array()){
        parent::__construct($values);

        $app = $this;

        $this->register(new ServiceProvider(), array(
            'db.options'     => array(
                'driver'   => 'pdo_mysql',
                'host'     => '127.0.0.1',
                'dbname'   => 'oauth_app',
                'user'     => 'php',
                'password' => 'password'
            ),
            'security.firewalls' => array(
                'admin'   => array(
                    'pattern' => '(^/admin|^/oauth/authorize)',
                    'form'    => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
                    'logout'  => array('logout_path' => '/admin/logout'),
                    'users'   => $app->share(function() use ($app) {
                        return new UserProvider($app);
                    })
                ),
                'default' => array(
                    'anonymous' => true
                )
            )
        ));

//TODO update schema conditionally
//        $schemaTool = new SchemaTool($this['orm.em']);
//        $schemaTool->updateSchema($this['orm.em']->getMetadataFactory()->getAllMetadata());


        $this['oauth.server.authorization'] = $this->share(function () use ($app) {
            $server = new AuthorizationServer();
            $server->setSessionStorage(new SessionStorage($app));
            $server->setAccessTokenStorage(new AccessTokenStorage($app));
            $server->setClientStorage(new ClientStorage($app));
            $server->setScopeStorage(new ScopeStorage($app));
            $server->setAuthCodeStorage(new AuthCodeStorage($app));
            $server->addGrantType(new AuthCodeGrant());
            return $server;
        });
        $this['oauth.server.resource'] = $this->share(function () use ($app) {
            return new ResourceServer(new SessionStorage($app), new AccessTokenStorage($app), new ClientStorage($app), new ScopeStorage($app));
        });


        $this->mount('/', new ControllerProvider());
    }

}