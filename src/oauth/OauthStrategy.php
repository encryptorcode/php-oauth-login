<?php
namespace encryptorcode\authentication\oauth;

interface OauthStrategy{
    public function getLoginUrl(string $state) : string;
    public function generateToken(string $grantCode) : OauthToken;
    public function regenerateToken(string $refreshToken) : OauthToken;
    public function revokeToken(string $refreshToken) : void;
    public function getUser(string $accessToken) : OauthUser;
}