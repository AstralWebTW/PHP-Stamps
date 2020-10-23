<?php

namespace AstralWeb\LibStamps\Web\Service\Soap;

class Client
{
    const ENV_PRODUCTION = 'prouduction';
    const ENV_STAGING = 'testing';


    protected static $_defaultOptions = [
        'trace' => true,
        'exception' => true
    ];

    protected $_soapClient;


    public function __construct(
        string $version,
        string $env = self::ENV_PRODUCTION)
    {
        $this->_soapClient = new \SoapClient(
            $this->_getUrlByEnvVersion($env, $version),
            static::$_defaultOptions
        );
    }

    /**
     * @param string $env
     * @param string $version
     * @return string
     */
    protected function _getUrlByEnvVersion(string $env, string $version)
    {
        switch ($env) {
            case static::ENV_PRODUCTION:

                return sprintf("https://swsim.stamps.com/swsim/swsimv%s.asmx?wsdl", $version);
                break;
            case static::ENV_STAGING:
            default:

                return sprintf("https://swsim.testing.stamps.com/swsim/swsimv%s.asmx?wsdl", $version);
                break;
        }
    }

    /**
     * @param string $endpoint
     * @param array $paramters
     * @param \AstralWeb\LibStamps\Web\Service\Soap\Response $formattedResponse
     * @return \AstralWeb\LibStamps\Web\Service\Soap\Response
     */
    public function makeRequest(
        string $endpoint,
        array $parameters = [],
        $formattedResponse = null)
    {
        if (is_null($formattedResponse)) {
            $formattedResponse = new \AstralWeb\LibStamps\Web\Service\Soap\Response();
        }

        $return = [
            'response' => null,
            'exception' => null,
            'headers_string' => null
        ];

        try {

            if ( ! $formattedResponse instanceof \AstralWeb\LibStamps\Web\Service\Soap\Response) {

                throw new \AstralWeb\LibStamps\Exception\ValidationError('Invalid response class!');
            }

            $return['response'] = $this->_soapClient->{$endpoint}($parameters);
            $return['headers_string'] = $this->_soapClient->__getLastResponseHeaders();

        } catch (\AstralWeb\LibStamps\Exception\ValidationError $ex) {

            $return['exception'] = $ex;

        } catch (\Exception $ex) {

            $return['headers_string'] = $this->_soapClient->__getLastResponseHeaders();
            $return['exception'] = $ex;
        }

        $formattedResponse->setContent($return);

        return $formattedResponse;
    }
}
