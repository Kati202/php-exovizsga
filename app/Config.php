<?php
namespace App;

class Config
{
    const MONGODB_HOST = 'localhost';
    const MONGODB_PORT = 27017;
    const MONGODB_DATABASE = 'exovizsga';
    
    public const BASE_URL = 'http://localhost/php-exovizsga/public/';
    //Kecskeméti urlek
    public const KECSO_URL = self::BASE_URL . 'kecso';
    public const KECSO_URL_CARDATA = self::BASE_URL . 'kecso/cardata';
    public const KECSO_URL_CARCOST = self::BASE_URL . 'kecso/carcost';
    public const KECSO_URL_COURIORDATA = self::BASE_URL . 'kecso/couriordata';
    public const KECSO_URL_COURIORADDRESS = self::BASE_URL . 'kecso/courioraddress';
    public const KECSO_URL_DEPO = self::BASE_URL . 'kecso/depo';
    public const KECSO_URL_DISP = self::BASE_URL . 'kecso/disp';

    //Tatabányai urlek
    public const TATAB_URL = self::BASE_URL . 'tatab';
    public const TATAB_URL_CARDATA = self::BASE_URL . 'tatab/cardata2';
    public const TATAB_URL_CARCOST = self::BASE_URL . 'tatab/carcost2';
    public const TATAB_URL_COURIORDATA = self::BASE_URL . 'tatab/couriordata2';
    public const TATAB_URL_COURIORADDRESS = self::BASE_URL . 'tatab/courioraddress2';
    public const TATAB_URL_DEPO = self::BASE_URL . 'tatab/depo2';
    public const TATAB_URL_DISP = self::BASE_URL . 'tatab/disp2';

    //Kinkunhalasi urlek
    public const HALAS_URL = self::BASE_URL . 'halas';
    public const HALAS_URL_CARDATA = self::BASE_URL . 'halas/cardata3';
    public const HALAS_URL_CARCOST = self::BASE_URL . 'halas/carcost3';
    public const HALAS_URL_COURIORDATA = self::BASE_URL . 'halas/couriordata3';
    public const HALAS_URL_COURIORADDRESS = self::BASE_URL . 'halas/courioraddress3';
    public const HALAS_URL_DEPO = self::BASE_URL . 'halas/depo3';
    public const HALAS_URL_DISP = self::BASE_URL . 'halas/disp3';

    //Nyergesújfalui urlek
    public const NYERGES_URL = self::BASE_URL . 'nyerges';
    public const NYERGES_URL_CARDATA = self::BASE_URL . 'nyerges/cardata4';
    public const NYERGES_URL_CARCOST = self::BASE_URL . 'nyerges/carcost4';
    public const NYERGES_URL_COURIORDATA = self::BASE_URL . 'nyerges/couriordata4';
    public const NYERGES_URL_COURIORADDRESS = self::BASE_URL . 'nyerges/courioraddress4';
    public const NYERGES_URL_DEPO = self::BASE_URL . 'nyerges/depo4';
    public const NYERGES_URL_DISP = self::BASE_URL . 'nyerges/disp4';


    public const HOME_URL = self::BASE_URL;
}
?>