<?php
namespace encryptorcode\authentication\server;

use encryptorcode\server\request\Request as Request;
use encryptorcode\authentication\service\AuthenticationHelper as AuthenticationHelper;
use encryptorcode\authentication\user\AuthUser as AuthUser;
use encryptorcode\authentication\session\AuthSession as AuthSession;

class AuthRequest extends Request{
    private $user;
    private $session;
    private $authenticated;

    private function __construct(){
        $this->authenticated = false;
    }

    private static $request;
    private static function request() : AuthRequest {
        if(!isset(self::$request)){
            self::$request = new self();
        }
        return self::$request;
    }
    
    public static function authenticate(AuthenticationHelper $helper) : void{
        session_start();

        $sessionIdentifier = parent::cookie(AUTH_COOKIE_NAME);
        if(!isset($sessionIdentifier)){
            return;
        }

        // fetching session from phpSession
        $session = null;
        $sessionStorage = $helper->getSessionStorage();
        // if(isset($_SESSION[AUTH_SESSION_KEY])){
        //     $session = $_SESSION[AUTH_SESSION_KEY];    
        // }
        if(!isset($session)){
            
            // fetching session from sessionStorage
            $session = $sessionStorage->getSession($sessionIdentifier);
            
            if(!isset($session)){
                return;
            }

            $_SESSION[AUTH_SESSION_KEY] = $session;
        }

        // updating the session storage about the last access time
        $sessionStorage->updateSessionAccessed($sessionIdentifier);
        
        $strategyLoader = $helper->getStrategyLoader();
        $token = $session->getToken();

        // fetching new access token if the token is expired
        if(self::isTimePassed($token->getExpiryTime())){
            $strategy = $strategyLoader->get($session->getOauthStrategy());
            $token = $strategy->regenerateToken($token->getRefreshToken());
            $newToken->setRefreshToken($token->getRefreshToken());
            $sessionStorage->updateSessionToken($sessionIdentifier,$newToken);
        }

        self::request()->user = $session->getUser();
        self::request()->session = $session;
        self::request()->authenticated = true;
    }

    private static function isTimePassed(int $time) : bool{
        return $time - time() < 0;
    }

    public static function authenticated() : bool{
        return self::request()->authenticated;
    }

    public static function user() : ?AuthUser{
        return self::request()->user;
    }

    public static function session() : ?AuthSession{
        return self::request()->session;
    }
}