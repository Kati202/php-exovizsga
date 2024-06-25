<?php
namespace App\Requests;

class Request
{
    public static function CarsInsert()
    {
        return isset($_POST['license']);
    }

    public static function CarsDelete()
    {
        return isset($_POST['deleteCarId']);
    }

    public static function CouriorsInsert()
    {
        return isset($_POST['ids']) && isset($_POST['name']);
    }

    public static function CouriorsDelete()
    {
        return isset($_POST['deleteCouriorId']);
    }

    public static function DepoInsert()
    {
      return isset($_POST['title']) && isset($_POST['content']);
    }
   
    public static function DepoSave()
    {
        return isset($_POST['editDepoId'], $_POST['title'], $_POST['content'], $_POST['saveDepo']);
    }
    public static function DepoEdit()
    {
      return isset($_POST['editDepoId'], $_POST['saveDepo']);
    }

    public static function DepoDelete()
    {
        return isset($_POST['deleteDepoId']);
    }
    public static function DepoUpdate()
    {
        return isset($_POST['updateDepoId'], $_POST['updateDepo']);
    }
}
?>