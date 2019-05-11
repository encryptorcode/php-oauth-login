<?php
namespace encryptorcode\authentication\implementation;

class OauthStrategyDetails{
    public $strategyName;
    public $loginUrl;
    public $clientId;
    public $clientSecret;
    public $redirectUri;
    public $tokenUrl;
    public $revokeUrl;
    public $userUrl;
    public $scope;

    public function __construct(string $loginUrl, string $clientId, string $clientSecret, string $redirectUri, string $tokenUrl, string $revokeUrl, string $userUrl, string $scope){
        $this->loginUrl = $loginUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->tokenUrl = $tokenUrl;
        $this->revokeUrl = $revokeUrl;
        $this->userUrl = $userUrl;
        $this->scope = $scope;
    }
}