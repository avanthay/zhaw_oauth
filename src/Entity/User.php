<?php


namespace Dave\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class User
 * @package Dave\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 *
 * @ORM\Entity()
 */
class User implements UserInterface {

    /**
     * @type int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @type string
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;

    /**
     * @type string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @type string
     * @ORM\Column(type="string", length=100)
     */
    protected $email;

    /**
     * @type string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @type array
     * @ORM\Column(type="array")
     */
    protected $roles;

    public function __construct($username, $password, array $roles){
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {}


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
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
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
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles) {
        $this->roles = $roles;
    }

}