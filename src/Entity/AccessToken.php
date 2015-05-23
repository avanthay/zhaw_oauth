<?php


namespace Dave\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\AbstractServer;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;


/**
 * Class AccessToken
 * @package Dave\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 *
 * @ORM\Entity()
 */
class AccessToken extends AccessTokenEntity {

    /**
     * @type string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @type int
     * @ORM\Column(type="integer")
     */
    protected $expireTime;

    /**
     * @type Session
     * @ORM\OneToOne(targetEntity="Session", mappedBy="accessToken", cascade={"persist", "remove"})
     */
    protected $session;
    
    public function __construct(AbstractServer $server, $token, $expireTime, Session $session){
        $this->setId($token);
        $this->setExpireTime($expireTime);
        $this->setSession($session);
        $session->setAccessToken($this);
        parent::__construct($server);
    }

    public function getSession() {
        return $this->session;
    }

    public function getScopes() {
        $this->session->getScopes();
    }

    public function associateScope(ScopeEntity $scope){
        if ($scope instanceof Scope) {
            $this->session->addScope($scope);
        } else {
            parent::associateScope($scope);
        }

        return $this;
    }

    public function setServer(AbstractServer $server){
        $this->server = $server;
    }

}