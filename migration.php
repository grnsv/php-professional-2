<?php

use Dotenv\Dotenv;
use App\Migrations\Migration_version_1;
use App\Migrations\Migration_version_2;
use App\Migrations\Migration_version_3;
use App\Migrations\Migration_version_4;
use App\Migrations\Migration_version_5;
use App\Migrations\Migration_version_6;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$migration = new Migration_version_1();
$migration->execute();

$migration = new Migration_version_2();
$migration->execute();

$migration = new Migration_version_3();
$migration->execute();

$migration = new Migration_version_4();
$migration->execute();

$migration = new Migration_version_5();
$migration->execute();

$migration = new Migration_version_6();
$migration->execute();
