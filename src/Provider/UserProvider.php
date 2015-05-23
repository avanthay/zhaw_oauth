<?php


namespace Dave\Provider;

use Silex\Application;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * Class UserProvider
 * @package Dave\Provider
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class UserProvider implements UserProviderInterface {

    private $app;
    
    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username) {
        $user = $this->app['orm.em']->getRepository('Dave\Entity\User')->findBy(array('username' => $username))[0];
        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user) {
        $refreshedUser = $this->app['orm.em']->getRepository('Dave\Entity\User')->find($user);
        if (!$refreshedUser) {
            throw new UnsupportedUserException();
        }

        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class) {
        return $class == 'Dave\Entity\User';
    }
}