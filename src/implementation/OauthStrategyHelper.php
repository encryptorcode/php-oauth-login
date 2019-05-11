<?php
namespace encryptorcode\authentication\implementation;

use encryptorcode\authentication\oauth\OauthUser as OauthUser;
use encryptorcode\authentication\oauth\OauthToken as OauthToken;

abstract class OauthStrategyHelper{
    public abstract function getDetails() : OauthStrategyDetails;
    public abstract function readUser(string $user) : OauthUser;
    public abstract function readToken(string $token) : OauthToken;
}