<?php


namespace Dave\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\AbstractServer;
use League\OAuth2\Server\Entity\ScopeEntity;


/**
 * Class Scope
 * @package Dave\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 *
 * @ORM\Entity()
 */
class Scope extends ScopeEntity {

    /**
     * @type int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @type Session
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="scopes")
     */
    protected $session;

    /**
     * @type string
     * @ORM\Column(type="string", length=60, nullable=false)
     */
    protected $name;

    /**
     * @type string
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $description;


    public function __construct(AbstractServer $server, $name, $description = null){
        $this->name = $name;
        $this->description = $description;
        parent::__construct($server);
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
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

}