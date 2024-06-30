<?php
namespace App\Requests;

class Request
{
    //Cars
    public static function CarsInsert()
    {
        return isset($_POST['license']);
    }

    public static function CarsDelete()
    {
        return isset($_POST['deleteCarId']);
    }


    //Couriors
    public static function CouriorsInsert()
    {
      return isset($_POST['name']) && isset($_POST['date']) && isset($_POST['dateaddress']) && isset($_POST['age']) && isset($_POST['address']) && isset($_POST['mothername']);
    }
   
    public static function CouriorSave()
    {
        return isset($_POST['editcouriorId'], $_POST['name'], $_POST['date'], $_POST['dateaddress'], $_POST['age'], $_POST['address'], $_POST['mothername'], $_POST['savecourior']);
    }
    public static function CouriorEdit()
    {
      return isset($_POST['editcouriorId'], $_POST['savecourior']);
    }

    public static function CouriorDelete()
    {
        return isset($_POST['deletecouriorId']);
    }
    public static function CouriorUpdate()
    {
        return isset($_POST['updatecouriorId'], $_POST['updatecourior']);
    }
  
    //Depos
    public static function DepoInsert()
    {
      return isset($_POST['title']) && isset($_POST['content']);
    }
   
    public static function DepoSave()
    {
        return isset($_POST['editDepoId'], $_POST['title'], $_POST['content']);
    }
    public static function DepoEdit()
    {
      return isset($_POST['editDepoId']);
    }

    public static function DepoDelete()
    {
        return isset($_POST['deleteDepoId']);
    }
    public static function DepoUpdate()
    {
        return isset($_POST['updateDepoId'], $_POST['updateDepo']);
    }


    //Disps
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