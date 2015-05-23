<?php


namespace Dave\Libraries\OAuth2Server;

use Dave\Entity\Session;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ClientInterface;
use Silex\Application;


/**
 * Class ClientStorage
 * @package Dave\Libraries\OAuth2Server
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class ClientStorage extends AbstractStorage implements ClientInterface {

    private $app;
    
    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * Validate a client
     *
     * @param string $clientId     The client's ID
     * @param string $clientSecret The client's secret (default = "null")
     * @param string $redirectUri  The client's redirect URI (default = "null")
     * @param string $grantType    The grant type used (default = "null")
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity | null
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null) {
        $client = $this->app['orm.em']->getRepository('Dave\Entity\Client')->find($clientId);
        //TODO check if any need for more implementation
        return $client;
    }

    /**
     * Get the client associated with a session
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity | null
     */
    public function getBySession(SessionEntity $session) {
        if (!$session instanceof Session) {
            $session = $this->app['orm.em']->getRepository('Dave\Entity\Session')->find($session->getId());
        }
        return $session->getClient();
    }


}