<?php
namespace AstralWeb\LibStamps;

class Example
{
    const TEST_VERSION = '101';
    const ENV_STAGING = \AstralWeb\LibStamps\Web\Service\Soap\Client::ENV_STAGING;

    static public function getAccountInfoInStaging($integrationId, $username, $password)
    {
        $client = new \AstralWeb\LibStamps\Web\Service\Soap\Client(static::TEST_VERSION, static::ENV_STAGING);

        $params = [
            'Credentials' => [
                'IntegrationID' => $integrationId,
                'Username'      => $username,
                'Password'      => $password
            ]
        ];
        $response = $client->makeRequest('GetAccountInfo', $params);


        if ( ! $response->getAuthenticatorForNextRequest()) {

            $msg = sprintf(
                    "<strong>Http Status Code:</strong> %s </br>
                     <strong>Response Headers String</strong>: %s </br>
                     <strong>Soap Fault Message</strong>: %s </br>
                     <strong>Exception Message</strong>: %s",
                $response->getHttpStatusCode(),
                $response->getHeadersString(),
                $response->getSoapFaultMessage(),
                $response->getException() ? $response->getException()->getMessage() : ''
            );

            echo $msg;
            return;
        }


        echo 'You got content: </br>';
        echo '<pre>';
        print_r($response->getRequestResponseArray());
        echo '</pre>';

        $authenticator = $response->getAuthenticatorForNextRequest();
        // Use authenticator to make next request without exposing credentials in every request
        $msg = sprintf("Please use the lastest Authenticator(<strong>%s</strong>) to make next request</br>", $authenticator);

        echo $msg;

        return $response;
    }
}
