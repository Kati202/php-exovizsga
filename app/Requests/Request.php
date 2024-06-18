<?php
namespace App\Requests;

class Request
{
    public static function CarsInsert()
    {
      return isset($_POST['license']) ;
    }
    public static function CarsDelete()
    {
        return isset($_POST['deleteCarId']);
    }
}
?>