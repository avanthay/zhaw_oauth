<?php


namespace Dave\Libraries\OAuth2Server;

use Dave\Entity\AuthCode;
use Dave\Entity\Scope;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AuthCodeInterface;
use Silex\Application;


/**
 * Class AuthCodeStorage
 * @package Dave\Libraries\OAuth2Server
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class AuthCodeStorage extends AbstractStorage implements AuthCodeInterface {

    private $app;
    
    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * Get the auth code
     *
     * @param string $code
     *
     * @return \League\OAuth2\Server\Entity\AuthCodeEntity | null
     */
    public function get($code) {
        $authCode = $this->app['orm.em']->getRepository('Dave\Entity\AuthCode')->find($code);
        $authCode->setServer($this->server);
        return $authCode;
    }

    /**
     * Create an auth code.
     *
     * @param string  $token       The token ID
     * @param integer $expireTime  Token expire time
     * @param integer $sessionId   Session identifier
     * @param string  $redirectUri Client redirect uri
     *
     * @return void
     */
    public function create($token, $expireTime, $sessionId, $redirectUri) {
        $session = $this->app['orm.em']->getRepository('Dave\Entity\Session')->find($sessionId);
        $authCode = $this->app['orm.em']->getRepository('Dave\Entity\AuthCode')->find($token);

        if (!$authCode) {
            $authCode = new AuthCode($this->getServer(), $token, $expireTime, $session, $redirectUri);

            $this->app['orm.em']->persist($authCode);
            $this->app['orm.em']->flush();
        }
    }

    /**
     * Get the scopes for an access token
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token) {
        if (!$token instanceof AuthCode) {
            $token = $this->app['orm.em']->getRepository('Dave\Entity\AuthCode')->find($token->getId());
        }
        return $token->getScopes();
    }

    /**
     * Associate a scope with an acess token
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param \League\OAuth2\Server\Entity\ScopeEntity    $scope The scope
     *
     * @return void
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope) {
        if (!$token instanceof AuthCode) {
            $token = $this->app['orm.em']->getRepository('Dave\Entity\AuthCode')->find($token->getId());
        }
        if (!$scope instanceof Scope) {
            $scope = $this->app['orm.em']->getRepository('Dave\Entity\Scope')->find($scope->getId());
        }

        $token->associateScope($scope);

        $this->app['orm.em']->persist($token);
        $this->app['orm.em']->flush();
    }

    /**
     * Delete an access token
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     *
     * @return void
     */
    public function delete(AuthCodeEntity $token) {
        if (!$token instanceof AuthCode) {
            $token = $this->app['orm.em']->getRepository('Dave\Entity\AuthCode')->find($token->getId());
        }
        $token->getSession()->setAuthCode(null);
        $token->setSession = null;

        $this->app['orm.em']->remove($token);
        $this->app['orm.em']->flush();
    }


}