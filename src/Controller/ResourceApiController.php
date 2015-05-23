<?php


namespace Dave\Controller;

use League\OAuth2\Server\Exception\OAuthException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class ResourceApiController
 * @package Dave\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class ResourceApiController {

    public function getAction(Application $app) {
        try {
            $app['oauth.server.resource']->isValidRequest(false);
        } catch (OAuthException $e) {
            return new JsonResponse(array('error' => $e->errorType, 'message' => $e->getMessage()), $e->httpStatusCode, $e->getHttpHeaders());
        }

        $userId = $app['oauth.server.resource']->getAccessToken()->getSession()->getOwnerId();
        $user = $app['orm.em']->getRepository('Dave\Entity\User')->find($userId);

        $userData = array('id' => $user->getId(), 'username' => $user->getUsername());

        $scopeProfile = $app['orm.em']->getRepository('Dave\Entity\Scope')->findOneBy(array('name' => 'profile'));
        $scopeEmail = $app['orm.em']->getRepository('Dave\Entity\Scope')->findOneBy(array('name' => 'email'));

        if ($app['oauth.server.resource']->getAccessToken()->getSession()->getScopes()->contains($scopeProfile)) {
            $userData['name'] = $user->getName();
        }
        if ($app['oauth.server.resource']->getAccessToken()->getSession()->getScopes()->contains($scopeEmail)) {
            $userData['email'] = $user->getEmail();
        }

        return new JsonResponse($userData);
    }

}