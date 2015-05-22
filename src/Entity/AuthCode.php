<?php


namespace Dave\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\AbstractServer;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;


/**
 * Class AuthCode
 * @package Dave\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 *
 * @ORM\Entity()
 */
class AuthCode extends AuthCodeEntity {

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @type string
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $redirectUri;

    /**
     * @type Session
     * @ORM\OneToOne(targetEntity="Session", inversedBy="authCode")
     */
    protected $session;


    public function __construct(AbstractServer $server, $token, $expireTime, Session $session, $redirectUri){
        $this->setId($token);
        $this->setExpireTime($expireTime);
        $this->setSession($session);
        $this->setRedirectUri($redirectUri);
        parent::__construct($server);
    }

    public function getScopes() {
        $this->session->getScopes();
    }

    public function getSession() {
        return $this->session;
    }

    public function associateScope(ScopeEntity $scope){
        if ($scope instanceof Scope) {
            $this->session->addScope($scope);
        } else {
            parent::associateScope($scope);
        }

        return $this;
    }
}