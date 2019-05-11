<?php
namespace encryptorcode\authentication\oauth;

class OauthUser{
    private $oauthId;
    private $email;
    private $fullName;
    private $name;
    private $profileImage;

    public function __construct(string $oauthId, string $email, string $fullName, string $name, string $profileImage) {
        $this->oauthId = $oauthId;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->name = $name;
        $this->profileImage = $profileImage;
    }

    public function getOauthId() : string {
        return $this->oauthId;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function getFullName() : string {
        return $this->fullName;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getProfileImage() : string {
        return $this->profileImage;
    }

}