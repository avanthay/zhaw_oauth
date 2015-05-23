<?php


namespace Dave\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entity\ClientEntity;


/**
 * Class Client
 * @package Dave\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 *
 * @ORM\Entity()
 */
class Client extends ClientEntity {

    /**
     * @type int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @type string
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $secret;

    /**
     * @type string
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $name;

    /**
     * @type Session
     * @ORM\OneToOne(targetEntity="Session", mappedBy="client")
     */
    protected $session;





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
    public function getSecret() {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret) {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return Session
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession($session) {
        $this->session = $session;
    }

}