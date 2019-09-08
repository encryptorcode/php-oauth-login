<?php
namespace encryptorcode\authentication\oauth;

class OauthToken implements \JsonSerializable{
    private $accessToken;
    private $refreshToken;
    private $expiryTime;

    public function __construct(?string $accessToken, ?string $refreshToken, ?int $expiryTime) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiryTime = $expiryTime;
    }

    public function getAccessToken() : string{
        return $this->accessToken;
    }

    public function getRefreshToken() : string {
        return $this->refreshToken;
    }

    public function getExpiryTime() : string{
        return $this->expiryTime;
    }

    public function setAccessToken(string $accessToken) : OauthToken {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function setRefreshToken(string $refreshToken) : OauthToken {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function setExpiryTime(int $expiryTime) : OauthToken {
        $this->expiryTime = $expiryTime;
        return $this;
    }

    public function jsonSerialize(){
        return [
            "accessToken" => $this->accessToken,
            "refreshToken" => $this->refreshToken,
            "expiryTime" => $this->expiryTime
        ];
    }

}