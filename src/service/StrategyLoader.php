<?php
namespace encryptorcode\authentication\service;

use encryptorcode\authentication\oauth\OauthStrategy as OauthStrategy;

interface StrategyLoader{
    function get(string $strategy) : OauthStrategy;
}