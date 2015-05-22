<?php


namespace Dave\Libraries\OAuth2Server;

use Dave\Entity\AccessToken;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use Silex\Application;


/**
 * Class AccessTokenStorage
 * @package Dave\Libraries\OAuth2Server
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class AccessTokenStorage extends AbstractStorage implements AccessTokenInterface {

    private $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * Get an instance of Entity\AccessTokenEntity
     *
     * @param string $token the access token
     *
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity | null
     */
    public function get($token) {
        return $this->app['orm.em']->getRepository('Dave\Entity\AccessToken')->find($token);
    }

    /**
     * Get the scopes for an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AccessTokenEntity $token) {
        return $token->getScopes();
    }

    /**
     * Creates a new access token
     *
     * @param string         $token      The access token
     * @param integer        $expireTime The expire time expressed as a unix timestamp
     * @param string|integer $sessionId  The session ID
     *
     * @return void
     */
    public function create($token, $expireTime, $sessionId) {
        $session = $this->app['orm.em']->getRepository('Dave\Entity\Session')->find($sessionId);
        $accessToken = new AccessToken($this->getServer(), $token, $expireTime, $session);

        $this->app['orm.em']->persist($accessToken);
        $this->app['orm.em']->flush();
    }

    /**
     * Associate a scope with an acess token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     * @param \League\OAuth2\Server\Entity\ScopeEntity       $scope The scope
     *
     * @return void
     */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope) {
        $token->associateScope($scope);

        $this->app['orm.em']->persist($token);
        $this->app['orm.em']->flush();
    }

    /**
     * Delete an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token to delete
     *
     * @return void
     */
    public function delete(AccessTokenEntity $token) {
        $this->app['orm.em']->remove($token);
        $this->app['orm.em']->flush();
    }


}