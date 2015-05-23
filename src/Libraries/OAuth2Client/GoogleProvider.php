<?php
/**
 * Class GoogleProvider
 * @package Dave\Libraries\OAuth2Client
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


namespace Dave\Libraries\OAuth2Client;


use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;

class GoogleProvider extends Google {

    public function getUserEmails(AccessToken $token) {
        $url = 'https://www.googleapis.com/gmail/v1/users/me/messages?maxResults=5&labelIds=Label_1';
        $messageUrl = 'https://www.googleapis.com/gmail/v1/users/me/messages/';

        $response = $this->fetchProviderData($url, $this->getHeaders($token));
        $messages = array();

        foreach (json_decode($response)->messages as $message) {
            $messages[] = json_decode($this->fetchProviderData($messageUrl . $message->id, $this->getHeaders($token)));
        }

        return $this->formatUserEmails($messages);
    }

    private function formatUserEmails($messages) {
        $formattedMessages = array();

        foreach ($messages as $message) {
            $formattedMessage = array(
                'id' => $message->id,
                'snippet' => $message->snippet
            );
            foreach ($message->payload->headers as $header) {
                $formattedMessage[strtolower($header->name)] = $header->value;
            }
            $formattedMessages[] = $formattedMessage;
        }

        return $formattedMessages;
    }

}