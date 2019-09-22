<?php
namespace encryptorcode\authentication\service;

use encryptorcode\server\request\Request as Request;
use encryptorcode\server\response\RedirectResponse as RedirectResponse;
use encryptorcode\authentication\server\AuthRequest as AuthRequest;
use encryptorcode\authentication\oauth\OauthException as OauthException;

define("AUTH_USER_KEY","encryptorcode.authentication.user");
define("AUTH_SESSION_KEY","encryptorcode.authentication.session");
define("AUTH_COOKIE_NAME","framework-auth");
define("SESSION_REDIRECT_KEY","encryptorcode.authentication.redirect");
define("INDEX_PATH","/");


class AuthenticationService{
    
    private $helper;
    private $authUserService;
    private $authSessionStorage;
    private $strategyLoader;

    public function __construct(AuthenticationHelper $helper){
        $this->helper = $helper;
        $this->authUserService = $helper->getUserService();
        $this->authSessionStorage = $helper->getSessionStorage();
        $this->strategyLoader = $helper->getStrategyLoader();
        AuthRequest::authenticate($helper);
    }

    public function getCurrentUser(){
        return AuthRequest::user();
    }

    public function getCurrentSession(){
        return AuthRequest::session();
    }

    public function loginPage() : void{
        $user = AuthRequest::user();
        if(isset($user)){
            $this->doRedirection(Request::param("redirect"));
            return;
        }

        $strategyName = Request::param("strategy");
        if(isset($strategyName)){
            $strategy = $this->strategyLoader->get($strategyName);
            $_SESSION[SESSION_REDIRECT_KEY] = Request::param("redirect");
            $this->doRedirection($strategy->getLoginUrl($strategyName));
            return;
        }
    }

    public function oauthCallback() : void{
        $strategyName = Request::param("state");
        $grantCode = Request::param("code");

        $strategy = $this->strategyLoader->get($strategyName);
        $token = $strategy->generateToken($grantCode);
        $oauthUser = $strategy->getUser($token->getAccessToken());
        $authUser = $this->authUserService->getUserByEmail($oauthUser->getEmail());

        if(!isset($authUser)){
            if(!$this->helper->isUserAllowedSignUp($oauthUser)){
                throw new OauthException("User was not allowed to signup.");
            }

            $strategyVsIdMap = array();
            $strategyVsIdMap[$strategyName] = $oauthUser->getOauthId();
            $this->authUserService->createUser(
                $oauthUser->getEmail(),
                $oauthUser->getName(),
                $oauthUser->getFullName(),
                $strategyVsIdMap,
                $oauthUser->getProfileImage()
            );

            $authUser = $this->authUserService->getUserByEmail($oauthUser->getEmail());
        } else {
            if(!$this->helper->isUserAllowedLogin($authUser)){
                throw new OauthException("User was not allowed to login.");
            }

            $strategyVsIdMap = $authUser->getStrategyVsIdMap();
            if(!isset($strategyVsIdMap) || count($strategyVsIdMap) == 0){
                $strategyVsIdMap = array();

                $authUser->setFullName($oauthUser->getFullName());
                $authUser->setName($oauthUser->getName());
                $authUser->setProfileImage($oauthUser->getProfileImage());
            }

            $strategyVsIdMap[$strategyName] = $oauthUser->getOauthId();
            $authUser->setStrategyVsIdMap($strategyVsIdMap);

            $this->authUserService->updateUser($authUser);
        }

        $sessionIdentifier = $this->generateSessionIdentifier();
        $this->authSessionStorage->createSession(
            $sessionIdentifier,
            $strategyName,
            $token,
            $authUser
        );

        $this->setAuthCookie($sessionIdentifier);
        $this->doRedirection($_SESSION[SESSION_REDIRECT_KEY]);
    }
    
    public function logout() : void{
        $sessionIdentifier = Request::cookie(AUTH_COOKIE_NAME);
        $this->clearAuthCookie();

        $session = AuthRequest::session();
        if(!isset($session)){
            $this->doRedirection(Request::param("redirect"));
        }

        $strategy = $this->strategyLoader->get($session->getOauthStrategy());
        if(isset($strategy)){
            $strategy->revokeToken($session->getToken()->getRefreshToken());
        }

        $this->authSessionStorage->deleteSession($sessionIdentifier);

        $this->doRedirection(Request::param("redirect"));
    }

    private function doRedirection(?string $redirectUrl) : void {
        if(!isset($redirectUrl)){
            $redirectUrl = INDEX_PATH;
        }
        $redirect = new RedirectResponse($redirectUrl);
        $redirect->respond();
    }

    private function generateSessionIdentifier() : string{
        return (string)random_int(10000,PHP_INT_MAX);
    }

    private function setAuthCookie(string $sessionIdentifier) : void{
        setcookie(AUTH_COOKIE_NAME, $sessionIdentifier, time() + (60 * 60 * 24 * 30), "/"); // FIXME:: setting 1 month span for cookie expiry. until a way to revoke unused tokens is found
    }

    private function clearAuthCookie() : void{
        setcookie(AUTH_COOKIE_NAME,"",1,"/");
    }

}