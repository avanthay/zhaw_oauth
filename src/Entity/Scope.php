<?php


namespace Dave\Entity;

use Doctrine\ORM\Mapping as ORM;
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

}