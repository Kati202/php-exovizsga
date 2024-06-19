<?php
namespace App;

class Config
{
    public const MONGODB_URI = 'mongodb://localhost:27017';
    public const DATABASE_NAME = 'exovizsga';
    
    public const BASE_URL = 'http://localhost/php-exovizsga/public/';
    //Kecskeméti urlek
    public const KECSO_URL = self::BASE_URL . 'kecso';
    public const KECSO_URL_CARDATA = self::BASE_URL . 'kecso/cardata';
    public const KECSO_URL_CARCOST = self::BASE_URL . 'kecso/carcost';
    public const KECSO_URL_COURIORDATA = self::BASE_URL . 'kecso/couriordata';
    public const KECSO_URL_COURIORADDRESS = self::BASE_URL . 'kecso/courioraddress';
    public const KECSO_URL_DEPO = self::BASE_URL . 'kecso/depo';

    public const TATA_URL = self::BASE_URL . 'tata';
    public const HALAS_URL = self::BASE_URL . 'halas';
    public const NYERGES_URL = self::BASE_URL . 'nyerges';
    public const HOME_URL = self::BASE_URL;
}
?>