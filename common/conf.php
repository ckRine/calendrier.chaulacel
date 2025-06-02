<?php

$host = $_SERVER['HTTP_HOST'];
if (str_contains($host, '.com')) {
    define("ENV_TYPE", "prod");
    define("ROOT_PATH", "");
} else {
    define("ENV_TYPE", "dev");
    define("ROOT_PATH", "/calendrier.chaulacel");
}

define('STATICS_PATH', ROOT_PATH.'/statics');