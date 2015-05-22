<?php


namespace Dave\Libraries\OAuth2Server;

use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ScopeInterface;
use Silex\Application;


/**
 * Class ScopeStorage
 * @package Dave\Libraries\OAuth2Server
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class ScopeStorage extends AbstractStorage implements ScopeInterface {

    private $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * Return information about a scope
     *
     * @param string $scope     The scope
     * @param string $grantType The grant type used in the request (default = "null")
     * @param string $clientId  The client sending the request (default = "null")
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity | null
     */
    public function get($scope, $grantType = null, $clientId = null) {
        $scopeEntity = $this->app['orm.em']->getRepository('Dave\Entity\Scope')->find($scope);
        //TODO check if any need for more implementation
        return $scopeEntity;
    }

}