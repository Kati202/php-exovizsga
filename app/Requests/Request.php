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
    public static function CouriorsInsert()
    {
      return isset($_POST['ids']);
      return isset($_POST['name']);
    }
    public static function CouriorsDelete()
    {
      return isset($_POST['deleteCouriorId']);
    }
}
?>