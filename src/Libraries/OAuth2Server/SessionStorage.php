<?php
/**
 * Class SessionStorage
 * @package Dave\Libraries\OAuth2Server
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


namespace Dave\Libraries\OAuth2Server;


use Dave\Entity\Session;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\SessionInterface;
use Silex\Application;

class SessionStorage extends AbstractStorage implements SessionInterface {

    private $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * Get a session from an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity | null
     */
    public function getByAccessToken(AccessTokenEntity $accessToken) {
        return $accessToken->getSession();
    }

    /**
     * Get a session from an auth code
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity | null
     */
    public function getByAuthCode(AuthCodeEntity $authCode) {
        return $authCode->getSession();
    }

    /**
     * Get a session's scopes
     *
     * @param  \League\OAuth2\Server\Entity\SessionEntity
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(SessionEntity $session) {
        return $session->getScopes();
    }

    /**
     * Create a new session
     *
     * @param string $ownerType         Session owner's type (user, client)
     * @param string $ownerId           Session owner's ID
     * @param string $clientId          Client ID
     * @param string $clientRedirectUri Client redirect URI (default = null)
     *
     * @return integer The session's ID
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null) {
        $client = $this->app['orm.em']->getRepository('Dave\Entity\Client')->find($clientId);
        $session = new Session($this->getServer(), $ownerType, $ownerId, $client, $clientRedirectUri);

        $this->app['orm.em']->persist($session);
        $this->app['orm.em']->flush();

        return $session->getId();
    }

    /**
     * Associate a scope with a session
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     * @param \League\OAuth2\Server\Entity\ScopeEntity   $scope   The scope
     *
     * @return void
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope) {
        $session->associateScope($scope);

        $this->app['orm.em']->persist($session);
        $this->app['orm.em']->flush();
    }
}