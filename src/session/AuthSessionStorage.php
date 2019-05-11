<?php
namespace encryptorcode\authentication\session;

use encryptorcode\authentication\user\AuthUser as AuthUser;
use encryptorcode\authentication\oauth\OauthToken as OauthToken;

interface AuthSessionStorage{
    function getSession(string $identifier) : ?AuthSession;
    function createSession(string $sessionIdentifier, string $strategyName, OauthToken $token, AuthUser $authUser) : void;
    function updateSessionToken(string $identifier, OauthToken $token) : void;
    function updateSessionAccessed(string $identifier) : void;
    function deleteSession(string $identifier) : void;
}