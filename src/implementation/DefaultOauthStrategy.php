<?php
namespace encryptorcode\authentication\implementation;

use encryptorcode\authentication\oauth\OauthStrategy as OauthStrategy;
use encryptorcode\authentication\oauth\OauthToken as OauthToken;
use encryptorcode\authentication\oauth\OauthUser as OauthUser;
use encryptorcode\httpclient\HttpRequest as HttpRequest;

abstract class DefaultOauthStrategy implements OauthStrategy{
    
    private $helper;
    private $details;

    public function __construct(OauthStrategyHelper $helper){
        $this->helper = $helper;
        $this->details = $helper->getDetails();
    }

    public function getLoginUrl(string $state) : string{
        $loginUrl = $this->details->loginUrl .
                "?client_id=" . $this->details->clientId .
                "&redirect_uri=" . $this->details->redirectUri .
                "&scope=" . $this->details->scope .
                "&access_type=offline" .
                "&response_type=code" .
                "&prompt=consent" .
                "&state=" . $state;
        return $loginUrl;
    }
    
    public function generateToken(string $grantCode) : OauthToken{
        $response = HttpRequest::post($this->details->tokenUrl)
            ->formParam("code",$grantCode)
            ->formParam("client_id",$this->details->clientId)
            ->formParam("client_secret",$this->details->clientSecret)
            ->formParam("redirect_uri",$this->details->redirectUri)
            ->formParam("grant_type","authorization_code")
            ->getResponse();

        $token = $this->helper->readToken($response->getBody());
        return $token;
    }
    
    public function regenerateToken(string $refreshToken) : OauthToken{
        $response = HttpRequest::post($this->details->tokenUrl)
            ->formParam("refresh_token",$refreshToken)
            ->formParam("client_id",$this->details->clientId)
            ->formParam("client_secret",$this->details->clientSecret)
            ->formParam("redirect_uri",$this->details->redirectUri)
            ->formParam("grant_type","authorization_code")
            ->getResponse();

        $token = $this->helper->readToken($response->getBody());
        return $token;
    }
    
    public function revokeToken(string $refreshToken) : void{
        $reponse = HttpRequest::post($this->details->revokeUrl)
            ->formParam("token",$refreshToken)
            ->getResponse();
    }
    
    public function getUser(string $accessToken) : OauthUser{
        $response = HttpRequest::get($this->details->userUrl)
            ->header("Authorization","Bearer ".$accessToken)
            ->getResponse();

        return $this->helper->readUser($response->getBody());
    }
}