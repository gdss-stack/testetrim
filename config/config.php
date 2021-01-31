<?php

define('SERVER', "127.0.0.1");

define('DB1', "pais");
define('USER1', "root", true);
define('PASS1', "", true);

define('SGDB1', "mysql:host=" . SERVER . ";dbname=" . DB1);

define('SERVERURL', "http://{$_SERVER['HTTP_HOST']}/testetrim/");