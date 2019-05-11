<?php
namespace encryptorcode\authentication\service;

use encryptorcode\authentication\user\AuthUserService as AuthUserService;
use encryptorcode\authentication\session\AuthSessionStorage as AuthSessionStorage;
use encryptorcode\authentication\oauth\OauthUser as OauthUser;
use encryptorcode\authentication\user\AuthUser as AuthUser;

abstract class AuthenticationHelper{
    public abstract function getStrategyLoader() : StrategyLoader;
    public abstract function getUserService() : AuthUserService;
    public abstract function getSessionStorage() : AuthSessionStorage;

    public function isUserAllowedSignUp(OauthUser $user) : bool{
        return true;
    }

    public function isUserAllowedLogin(AuthUser $user) : bool{
        return true;
    }
}