<?php


namespace Dave\Controller;

use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Util\RedirectUri;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class AuthorizationController
 * @package Dave\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class AuthorizationController {

    public function authorizeAction(Request $request, Application $app) {
        try {
            $auth = $app['oauth.server.authorization']->getGrantType('authorization_code')->checkAuthorizeParams();
        } catch (OAuthException $e) {
            if ($e->shouldRedirect()) {
                return new RedirectResponse($e->getRedirectUri());
            }

            return new JsonResponse(array('error' => $e->errorType, 'message' => $e->getMessage()), $e->httpStatusCode, $e->getHttpHeaders());
        }

        if ($authForm = $request->request->get('authorization')) {
            if ($authForm == 'Approve') {
                return new RedirectResponse($app['oauth.server.authorization']->getGrantType('authorization_code')->newAuthorizeRequest('user', 1, $auth));
            }

            $error = new AccessDeniedException();

            return new RedirectResponse(new RedirectUri($auth['redirect_uri'], array(
                'error'   => $error->errorType,
                'message' => $error->getMessage()
            )));
        }

        return $app['twig']->render('authorize.twig', array('client' => $auth['client']));
    }

    public function accessTokenAction(Application $app) {
        try {
            return new JsonResponse($app['oauth.server.authorization']->issueAccessToken(), 200, array(
                'Content-type'  => 'application/json',
                'Cache-Control' => 'no-store',
                'Pragma'        => 'no-store'
            ));
        } catch (OAuthException $e) {
            return new JsonResponse(array('error' => $e->errorType, 'message' => $e->getMessage()), $e->httpStatusCode, $e->getHttpHeaders());
        }
    }

}