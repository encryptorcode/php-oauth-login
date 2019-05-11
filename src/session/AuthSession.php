<?php
namespace encryptorcode\authentication\session;

use encryptorcode\authentication\user\AuthUser as AuthUser;
use encryptorcode\authentication\oauth\OauthToken as OauthToken;

interface AuthSession{
    function getIdentifier() : string;
    function getOauthStrategy() : string;
    function getUser() : AuthUser;
    function getToken() : OauthToken;
}