<?php


require_once 'EBSCOConnector.php';
require_once 'EBSCOResponse.php';
require_once 'EBSCOAuthenticateIP.php';


class EBSCOAPI
{

    private $authenticationToken;


    private $sessionToken;


    private $connector;


    public function apiEndSessionToken($authenToken, $sessionToken)
    {


        $headers = array(
            'x-authenticationToken: ' . $authenToken
        );

        $this->connector()->requestEndSessionToken($headers, $sessionToken);
    }


    public function connector()
    {
        if (empty($this->connector)) {
            $this->connector = new EBSCOConnector();
        }

        return $this->connector;
    }


    public function apiSearch($params)
    {

        $results = $this->request('Search', $params);
        return $results;
    }


    protected function request($action, $params = null, $attempts = 3)
    {
        try {

            $authenticationToken = $this->getAuthToken();
            $sessionToken = $this->getSessionToken($authenticationToken);


            if (empty($authenticationToken)) {
                $authenticationToken = $this->getAuthToken();
            }

            if (empty($sessionToken)) {
                $sessionToken = $this->getSessionToken($authenticationToken, 'y');
            }

            $headers = array(
                'x-authenticationToken: ' . $authenticationToken,
                'x-sessionToken: ' . $sessionToken
            );

            $response = call_user_func_array(array($this->connector(), "request{$action}"), array($params, $headers));

            $result = $this->response($response)->result();
            $results = $result;
            return $results;
        } catch (EBSCOException $e) {
            try {

                $code = $e->getCode();
                switch ($code) {
                    case EBSCOConnector::EDS_AUTH_TOKEN_INVALID:
                        $authenticationToken = $this->getAuthToken();
                        $sessionToken = $this->getSessionToken($authenticationToken);
                        $headers = array(
                            'x-authenticationToken: ' . $authenticationToken,
                            'x-sessionToken: ' . $sessionToken
                        );
                        if ($attempts > 0) {
                            return $this->request($action, $params, $headers, --$attempts);
                        }
                        break;
                    case EBSCOConnector::EDS_SESSION_TOKEN_INVALID:
                        $sessionToken = $this->getSessionToken($authenticationToken, 'y');
                        $headers = array(
                            'x-authenticationToken: ' . $authenticationToken,
                            'x-sessionToken: ' . $sessionToken
                        );
                        if ($attempts > 0) {
                            return $this->request($action, $params, $headers, --$attempts);
                        }
                        break;
                    default:
                        $result = array(
                            'error' => $e->getMessage()
                        );
                        return $result;
                        break;
                }
            } catch (Exception $e) {
                $result = array(
                    'error' => $e->getMessage()
                );
                return $result;
            }
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage()
            );
            return $result;
        }
    }

    public function getAuthToken()
    {
        $timestamp = time();
        $timeout = 0;
        if (isset($_SESSION["authenticationToken"])) {
            $this->authToken = $_SESSION["authenticationToken"];
            $timeout = $_SESSION["authenticationTimeout"] - 600;
            $timestamp = $_SESSION["authenticationTimeStamp"];
        } else {
            $result = $this->apiAuthenticationToken();
            $_SESSION["authenticationToken"] = $result['authenticationToken'];
            $_SESSION["authenticationTimeout"] = $result['authenticationTimeout'];
            $_SESSION["authenticationTimeStamp"] = $result['authenticationTimeStamp'];
        }

        if (time() - $timestamp >= $timeout) {
            $result = $this->apiAuthenticationToken();
            $_SESSION["authenticationToken"] = $result['authenticationToken'];
            $_SESSION["authenticationTimeout"] = $result['authenticationTimeout'];
            $_SESSION["authenticationTimeStamp"] = $result['authenticationTimeStamp'];

            return $result['authenticationToken'];
        } else {
            return $this->authToken;
        }

    }


    public function apiAuthenticationToken()
    {
        $response = $this->connector()->requestAuthenticationToken();
        $result = $this->response($response)->result();
        return $result;
    }


    public function response($response)
    {
        $responseObj = new EBSCOResponse($response);
        return $responseObj;
    }


    public function getSessionToken($authenToken, $guest = 'n')
    {
        $token = '';
        $configFile = "config.xml";


        if (isset($_SESSION['login']) or (validAuthIP($configFile) == true)) {
            if (($guest == 'n') or (validAuthIP($configFile) == true)) {
                $sessionToken = $this->apiSessionToken($authenToken, 'n');
                $_SESSION['sessionToken'] = $sessionToken;
            }
            $token = $_SESSION['sessionToken'];
        } else {
            $sessionToken = $this->apiSessionToken($authenToken, 'y');
            $_SESSION['sessionToken'] = $sessionToken;

            $token = $_SESSION['sessionToken'];

        }
        return $token;
    }


    public function apiSessionToken($authenToken, $guest = "y")
    {

        $headers = array(
            'x-authenticationToken: ' . $authenToken
        );

        $response = $this->connector()->requestSessionToken($headers, $guest);

        $result = $this->response($response)->result();

        return $result;
    }


    public function apiRetrieve($an, $db, $term)
    {

        $params = array(
            'an' => $an,
            'dbid' => $db,
            'highlightterms' => $term
        );
        $params = http_build_query($params);
        $result = $this->request('Retrieve', $params);
        return $result;
    }


    public function getInfo()
    {
        if (isset($_SESSION['info'])) {
            $InfoArray = $_SESSION['info'];
            $timestamp = $InfoArray['timestamp'];
            if (time() - $timestamp >= 3600) {

                $InfoArray = $this->apiInfo();
                $_SESSION['info'] = $InfoArray;
                $info = $InfoArray['Info'];
            } else {
                $info = $InfoArray['Info'];
            }
        } else {

            $InfoArray = $this->apiInfo();
            $_SESSION['info'] = $InfoArray;
            $info = $InfoArray['Info'];
        }
        return $info;
    }

    public function apiInfo()
    {

        $response = $this->request('Info');

        $Info = array(
            'Info' => $response,
            'timestamp' => time()
        );
        return $Info;
    }
}
