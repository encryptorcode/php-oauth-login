<?php
namespace encryptorcode\authentication\user;

interface AuthUserService{
    function getUserByEmail(string $email) : ?AuthUser;
    function createUser(string $email, string $name, string $fullName, array $strategyVsIdMap, string $profileImage) : void;
    function updateUser(AuthUser $user) : void;
}