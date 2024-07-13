<?php
namespace App\Requests;

class KecsoRequest
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
    public static function CarCostInsert()
    {
        return isset($_POST['ids']) && isset($_POST['date']) && isset($_POST['part']) &&
               isset($_POST['cost']);
    }
    public static function CarCostDelete()
    {
        return isset($_POST['deleteCarcostId']) && isset($_POST['deleteCarcost']);
    }

    public static function CarCostUpdate()
    {
        return isset($_POST['updateCarCostId']) && isset($_POST['updateCarcost']);
    }

    public static function CarCostSave()
    {
        return isset($_POST['editCarcostId']) && isset($_POST['saveCarCos']) &&
               isset($_POST['ids']) && isset($_POST['date']) && isset($_POST['part']) &&
               isset($_POST['cost']) && isset($_POST['selectTime']);
    }
  

    //Couriors
    public static function CouriorInsert()
    {
        return isset($_POST['ids'], $_POST['name'], $_POST['newCourior']);
    }
    
    
    public static function CouriorSave()
    {
        return isset($_POST['editCouriorId'],$_POST['saveCouriordata'], $_POST['name'], $_POST['date'], $_POST['dateaddress'], $_POST['age'], $_POST['address'], $_POST['mothername']);
    }
    
    public static function CouriorEdit()
    {
        return isset($_POST['editCouriorId'], $_POST['saveCouriordata']);
    }
    
    public static function CouriorDelete()
    {
        return isset($_POST['deleteCouriorId'], $_POST['deleteCourior']);
    }
    
    public static function CouriorUpdate()
    {
        return isset($_POST['updateCouriorId'], $_POST['updateCourior']);
    }
    
    public static function CouriorsInsert()
    {
        return isset($_POST['name'], $_POST['date'], $_POST['dateaddress'], $_POST['age'], $_POST['address'], $_POST['mothername'], $_POST['newCouriors']);
    }
    public static function AddressInsert()
    {
        return isset($_POST['day']) && isset($_POST['month']) && isset($_POST['time']) &&
               isset($_POST['total_addresses']) && isset($_POST['delivered_addresses']) &&
               isset($_POST['final_return']) && isset($_POST['live_return']) && isset($_POST['newAddress']);
    }
    public static function AddressDelete()
    {
        return isset($_POST['deleteAddressId']) && isset($_POST['deleteAddress']);
    }

    public static function AddressUpdate()
    {
        return isset($_POST['updateAddressId']) && isset($_POST['updateAddress']);
    }

    public static function AddressSave()
    {
        return isset($_POST['editAddressId']) && isset($_POST['saveAddress']) &&
               isset($_POST['day']) && isset($_POST['month']) && isset($_POST['time']) &&
               isset($_POST['total_addresses']) && isset($_POST['delivered_addresses']) &&
               isset($_POST['final_return']) && isset($_POST['live_return'])&& isset($_POST['selectTime']);
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