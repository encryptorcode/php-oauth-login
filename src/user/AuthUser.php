<?php
namespace encryptorcode\authentication\user;

interface AuthUser{
    function getName() : string;
    function getFullName() : string;
    function getEmail() : string;
    function getStrategyVsIdMap() : array;
    function getProfileImage() : string;

    function setName(string $name) : void;
    function setFullName(string $fullName) : void;
    function setEmail(string $email) : void;
    function setStrategyVsIdMap(array $strategyVsIdMap) : void;
    function setProfileImage(string $profileImage) : void;
}