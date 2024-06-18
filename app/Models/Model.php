<?php
namespace App\Models;
use App\Requests\Request;
use App\Config;

class Model
{
    private static $db;

    public static function Init()
    {
        $uri =Config::MONGODB_URI;
        $client = new \MongoDB\Client($uri);
        
        self::$db = $client->selectDatabase(Config::DATABASE_NAME);
        self::$db->createCollection('kecsocar');

        self::$db->createCollection('kecsocarcost');
    }
    
    public static function InsertCar($car)
    {
        $collection = self::$db->kecsocar;
        return $collection->insertOne($car);
    }
    public static function InsertCarCost($carCost)
{
    $collection = self::$db->kecsocarcost;
    $result = $collection->insertOne($carCost);

    return $result->getInsertedId();
}


     public static function GetCars()
     {
         $collection = self::$db->kecsocar;
         $options = ['sort' => ['license' => 1]];
         $list = $collection->find([], $options);
         
         return $list->toArray();
     }
    public static function GetCarCosts()
{
    $collection = self::$db->kecsocarcost;

    try {
        $cursor = $collection->find();
        return $cursor->toArray();
    } catch (\Exception $e) {
        // Hiba kezelése (pl. logolás vagy kivétel dobása)
        return [];
    }
}


public static function GetCarCostsByCarId($carId)
{
    $collection = self::$db->kecsocarcost;
    $carCosts = $collection->find(['carId' => new \MongoDB\BSON\ObjectId($carId)]);

    return $carCosts->toArray();
}
    public static function UpdateCar($id, $data)
    {
        $collection = self::$db->kecsocar;
        return $collection->updateOne(self::CreateFilterById($id), ['$set' => $data]);
    }



    public static function DeleteCar($id)
    {
        $collection = self::$db->kecsocar;
        $result = $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        return $result->getDeletedCount();
    }
    public static function DeleteCarCost($id)
{
    $collection = self::$db->kecsocarcost;

    try {
        $result = $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return $result->getDeletedCount();
    } catch (\Exception $e) {
        // Hiba kezelése (pl. logolás vagy kivétel dobása)
        return 0;
    }
}
    
    private static function CreateFilterById($id)
    {
        if(!($id instanceof \MongoDB\BSON\ObjectId))
        {
            $id = new \MongoDB\BSON\ObjectId($id);
        }
        $filter = ['_id' => $id];
        return $filter;
    }
}
?>