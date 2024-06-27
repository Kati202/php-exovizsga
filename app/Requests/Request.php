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
    public static function DispInsert()
    {
        return isset($_POST['name']) && isset($_POST['title']) && isset($_POST['phone']) && isset($_POST['newDisp']);
    }

    public static function DispSave()
    {
        return isset($_POST['editDispId'], $_POST['name'], $_POST['title'], $_POST['phone'], $_POST['saveDisp']);
    }

    public static function DispEdit()
    {
        return isset($_POST['editDispId'], $_POST['saveDisp']);
    }

    public static function DispDelete()
    {
        return isset($_POST['deleteDispId']);
    }

    public static function DispUpdate()
    {
        return isset($_POST['updateDispId'], $_POST['updateDisp']);
    }

}
?>