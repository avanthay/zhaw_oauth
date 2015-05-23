<?php


namespace Dave\Libraries\OAuth2Client;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;


/**
 * Class OwnServerProvider
 * @package Dave\Libraries\OAuth2Client
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class OwnServerProvider extends AbstractProvider {

    private $host = 'http://oauth';
    public $authorizationHeader = 'Bearer';

    /**
     * Get the URL that this provider uses to begin authorization.
     *
     * @return string
     */
    public function urlAuthorize() {
        return $this->host . '/oauth/authorize';
    }

    /**
     * Get the URL that this provider users to request an access token.
     *
     * @return string
     */
    public function urlAccessToken() {
        return $this->host . '/oauth/token';
    }

    /**
     * Get the URL that this provider uses to request user details.
     *
     * Since this URL is typically an authorized route, most providers will require you to pass the access_token as
     * a parameter to the request. For example, the google url is:
     *
     * 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='.$token
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function urlUserDetails(AccessToken $token) {
        return $this->host . '/api';
    }

    /**
     * Given an object response from the server, process the user details into a format expected by the user
     * of the client.
     *
     * @param object      $response
     * @param AccessToken $token
     *
     * @return mixed
     */
    public function userDetails($response, AccessToken $token) {
        return $response;
    }
}