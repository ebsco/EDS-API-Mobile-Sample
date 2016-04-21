<?php


class EBSCOException extends Exception
{
}


class EBSCOConnector
{

    const EDS_UNKNOWN_PARAMETER = 100;
    const EDS_INCORRECT_PARAMETER_FORMAT = 101;
    const EDS_INVALID_PARAMETER_INDEX = 102;
    const EDS_MISSING_PARAMETER = 103;
    const EDS_AUTH_TOKEN_INVALID = 104;
    const EDS_INCORRECT_ARGUMENTS_NUMBER = 105;
    const EDS_UNKNOWN_ERROR = 106;
    const EDS_AUTH_TOKEN_MISSING = 107;
    const EDS_SESSION_TOKEN_MISSING = 108;
    const EDS_SESSION_TOKEN_INVALID = 109;
    const EDS_INVALID_RECORD_FORMAT = 110;
    const EDS_UNKNOWN_ACTION = 111;
    const EDS_INVALID_ARGUMENT_VALUE = 112;
    const EDS_CREATE_SESSION_ERROR = 113;
    const EDS_REQUIRED_DATA_MISSING = 114;
    const EDS_TRANSACTION_LOGGING_ERROR = 115;
    const EDS_DUPLICATE_PARAMETER = 116;
    const EDS_UNABLE_TO_AUTHENTICATE = 117;
    const EDS_SEARCH_ERROR = 118;
    const EDS_INVALID_PAGE_SIZE = 119;
    const EDS_SESSION_SAVE_ERROR = 120;
    const EDS_SESSION_ENDING_ERROR = 121;
    const EDS_CACHING_RESULTSET_ERROR = 122;


    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;


    private static $end_point;


    private static $authentication_end_point;


    private $password;


    private $userId;


    private $interfaceId;


    private $orgId;


    public function __construct()
    {
        $xml = "Config.xml";
        $dom = simplexml_load_file($xml);

        self::$end_point = $dom->EndPoints->EndPoint;
        self::$authentication_end_point = $dom->EndPoints->AuthenticationEndPoint;

        $EDSCredentials = $dom->EDSCredentials;
        $this->userId = (string)$EDSCredentials->EDSUserID;
        $this->password = (string)$EDSCredentials->EDSPassword;
        $this->interfaceId = (string)$EDSCredentials->EDSProfile;
        $this->orgId = '';
    }


    public function requestAuthenticationToken()
    {
        $url = self::$authentication_end_point . '/UIDAuth';

        $params = <<<BODY
<UIDAuthRequestMessage xmlns="http://www.ebscohost.com/services/public/AuthService/Response/2012/06/01">
    <UserId>{$this->userId}</UserId>
    <Password>{$this->password}</Password>
    <InterfaceId>{$this->interfaceId}</InterfaceId>
</UIDAuthRequestMessage>
BODY;


        $headers = array(
            'Content-Type: application/xml',
            'Content-Length: ' . strlen($params)
        );

        $response = $this->request($url, $params, $headers, 'POST');
        return $response;
    }


    protected function request($url, $params = null, $headers = null, $method = 'GET')
    {
        $log = fopen('curl.log', 'w');
        $xml = false;


        $ch = curl_init();


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $log);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


        if (empty($params)) {

            curl_setopt($ch, CURLOPT_URL, $url);
        } else {

            if ($method == 'GET') {
                $url .= '?' . $params;
                curl_setopt($ch, CURLOPT_URL, $url);

            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }


        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }


        $response = curl_exec($ch);


        if (strstr($url, 'Search')) {
            $_SESSION['resultxml'] = $response;
        }
        if (strstr($url, 'Retrieve')) {
            $_SESSION['recordxml'] = $response;
        }


        if ($response === false) {
            fclose($log);
            throw new Exception(curl_error($ch));
            curl_close($ch);
        } else {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            fclose($log);
            curl_close($ch);
            switch ($code) {
                case self::HTTP_OK:
                    $xml = simplexml_load_string($response);
                    if ($xml === false) {
                        throw new Exception('Error while parsing the response.');
                    } else {
                        return $xml;
                    }
                    break;
                case self::HTTP_BAD_REQUEST:
                    $xml = simplexml_load_string($response);
                    if ($xml === false) {
                        throw new Exception('Error while parsing the response.');
                    } else {

                        $error = '';
                        $code = 0;
                        $isError = isset($xml->ErrorNumber) || isset($xml->ErrorCode);
                        if ($isError) {
                            if (isset($xml->DetailedErrorDescription) && !empty($xml->DetailedErrorDescription)) {
                                $error = (string)$xml->DetailedErrorDescription;
                            } else if (isset($xml->ErrorDescription)) {
                                $error = (string)$xml->ErrorDescription;
                            } else if (isset($xml->Reason)) {
                                $error = (string)$xml->Reason;
                            }
                            if (isset($xml->ErrorNumber)) {
                                $code = (integer)$xml->ErrorNumber;
                            } else if (isset($xml->ErrorCode)) {
                                $code = (integer)$xml->ErrorCode;
                            }
                            throw new EBSCOException($error, $code);
                        } else {
                            throw new Exception('The request could not be understood by the server
                            due to malformed syntax. Modify your search before retrying.');
                        }
                    }
                    break;
                case self::HTTP_NOT_FOUND:
                    throw new Exception('The resource you are looking for might have been removed,
                        had its name changed, or is temporarily unavailable.');
                    break;
                case self::HTTP_INTERNAL_SERVER_ERROR:
                    throw new Exception('The server encountered an unexpected condition which prevented
                        it from fulfilling the request.');
                    break;

                default:
                    throw new Exception('Unexpected HTTP error.');
                    break;
            }
        }
    }


    public function requestSessionToken($headers, $guest = "y")
    {
        $url = self::$end_point . '/CreateSession';


        $params = array(
            "profile" => $this->interfaceId,
            "org" => $this->orgId,
            "guest" => $guest
        );
        $params = http_build_query($params);

        $response = $this->request($url, $params, $headers);

        return $response;
    }


    public function requestEndSessionToken($headers, $sessionToken)
    {
        $url = self::$end_point . '/endsession';


        $params = array(
            'sessiontoken' => $sessionToken
        );
        $params = http_build_query($params);
        $this->request($url, $params, $headers);
    }


    public function requestSearch($params, $headers)
    {
        $url = self::$end_point . '/Search';

        $response = $this->request($url, $params, $headers);
        return $response;
    }


    public function requestRetrieve($params, $headers)
    {
        $url = self::$end_point . '/Retrieve';

        $response = $this->request($url, $params, $headers);
        return $response;
    }


    public function requestInfo($params, $headers)
    {
        $url = self::$end_point . '/Info';

        $response = $this->request($url, $params, $headers);
        return $response;
    }
}


?>