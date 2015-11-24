<?php
/**
 * OpenPayU Standard Library
 *
 * @copyright  Copyright (c) 2011-2015 PayU
 * @license    http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 * http://www.payu.com
 * http://developers.payu.com
 */

class OpenPayU_HttpCurl
{
    /**
     * @var
     */
    static $headers;

    /**
     * @param $requestType
     * @param string $pathUrl
     * @param $data
     * @param $posId
     * @param $signatureKey
     * @return mixed
     * @throws OpenPayU_Exception_Configuration
     * @throws OpenPayU_Exception_Network
     * @throws OpenPayU_Exception_Authorization
     */
    public static function doRequest($requestType, $pathUrl, $data, $posId, $signatureKey)
    {
        if (empty($pathUrl))
            throw new OpenPayU_Exception_Configuration('The endpoint is empty');

        if (empty($posId)) {
            throw new OpenPayU_Exception_Configuration('PosId is empty');
        }

        if (empty($signatureKey)) {
            throw new OpenPayU_Exception_Configuration('SignatureKey is empty');
        }

        $userNameAndPassword = $posId.":".$signatureKey;

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );

        $ch = curl_init($pathUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'OpenPayU_HttpCurl::readHeader');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $userNameAndPassword);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($response === false) {
            throw new OpenPayU_Exception_Network(curl_error($ch));
        }
        curl_close($ch);

        return array('code' => $httpStatus, 'response' => trim($response));
    }

    /**
     * @param array $headers
     *
     * @return mixed
     */
    public static function getSignature($headers)
    {
        foreach($headers as $name => $value)
        {
            if(preg_match('/X-OpenPayU-Signature/i', $name) || preg_match('/OpenPayu-Signature/i', $name))
                return $value;
        }

        return null;
    }

    /**
     * @param resource $ch
     * @param string $header
     * @return int
     */
    public static function readHeader($ch, $header)
    {
        if( preg_match('/([^:]+): (.+)/m', $header, $match) ) {
            self::$headers[$match[1]] = trim($match[2]);
        }

        return strlen($header);
    }

}
