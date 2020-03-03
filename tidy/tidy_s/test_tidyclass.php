<?php
    require_once("vendor/autoload.php");
    use \Firebase\JWT\JWT;
    use \Curl\Curl;

    class TidyApi
    {
        const CLIENT_ID  = "32f8b7a3";
        const SECRET_KEY = "fe63b0b8dcb16e71beb88dc923a9a381";
        const API_URL    = "https://staging-v1-api.tidy.zone/";

        public static function call($uri, $method, Array $data = [])
        {
            $curl = new Curl();
            $data['client_id'] = self::CLIENT_ID;
            $token = self::generateToken($data);
            $curl->setHeader('Authorization', 'Bearer '.$token);
            $method = strtolower($method);
            if($uri != 'api/user/outside/info' && $uri != 'api/user/outside')
                $data['token'] = $token;
            $curl->{$method}(self::API_URL.$uri, $data);

            return json_decode($curl->response, true);
        }

        private static function generateToken(Array $data)
        {
            $data['iat'] = (int)microtime(true);
            $jwt = JWT::encode($data, self::SECRET_KEY);

            return $jwt;
        }
    }
?>