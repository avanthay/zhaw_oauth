<?php


namespace Dave\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\AbstractServer;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;

/**
 * Class Session
 * @package Dave\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 *
 * @ORM\Entity()
 */
class Session extends SessionEntity {

    /**
     * @type int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @type string
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $ownerType;

    /**
     * @type string
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $ownerId;

    /**
     * @type string
     * @ORM\OneToOne(targetEntity="Client", inversedBy="session", cascade={"persist", "remove"})
     */
    protected $client;

    /**
     * @type string
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $clientRedirectUri;

    /**
     * @type AuthCode
     * @ORM\OneToOne(targetEntity="AuthCode", inversedBy="session", cascade={"persist", "remove"})
     */
    protected $authCode;

    /**
     * @type AccessToken
     * @ORM\OneToOne(targetEntity="AccessToken", inversedBy="session", cascade={"persist", "remove"})
     */
    protected $accessToken;

    /**
     * @type ArrayCollection
     * @ORM\OneToMany(targetEntity="Scope", mappedBy="session", cascade={"persist", "remove"})
     */
    protected $scopes;


    public function __construct(AbstractServer $server, $ownerType, $ownerId, $client, $clientRedirectUri) {
        $this->ownerType = $ownerType;
        $this->ownerId = $ownerId;
        $this->client = $client;
        $this->clientRedirectUri = $clientRedirectUri;
        $this->scopes = new ArrayCollection();
        parent::__construct($server);
    }

    public function addScope(Scope $scope) {
        $this->scopes->add($scope);
    }

    public function removeScope(Scope $scope) {
        $this->scopes->removeElement($scope);
    }

    public function associateScope(ScopeEntity $scope) {
        if ($scope instanceof Scope) {
            $this->addScope($scope);
        } else {
            parent::associateScope($scope);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOwnerType() {
        return $this->ownerType;
    }

    /**
     * @param string $ownerType
     */
    public function setOwnerType($ownerType) {
        $this->ownerType = $ownerType;
    }

    /**
     * @return string
     */
    public function getOwnerId() {
        return $this->ownerId;
    }

    /**
     * @param string $ownerId
     */
    public function setOwnerId($ownerId) {
        $this->ownerId = $ownerId;
    }

    /**
     * @return string
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @param string $client
     */
    public function setClient($client) {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getClientRedirectUri() {
        return $this->clientRedirectUri;
    }

    /**
     * @param string $clientRedirectUri
     */
    public function setClientRedirectUri($clientRedirectUri) {
        $this->clientRedirectUri = $clientRedirectUri;
    }

    /**
     * @return AuthCode
     */
    public function getAuthCode() {
        return $this->authCode;
    }

    /**
     * @param AuthCode $authCode
     */
    public function setAuthCode($authCode) {
        $this->authCode = $authCode;
    }

    /**
     * @return ArrayCollection
     */
    public function getScopes() {
        return $this->scopes;
    }

    /**
     * @param ArrayCollection $scopes
     */
    public function setScopes($scopes) {
        $this->scopes = $scopes;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * @param AccessToken $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }
}