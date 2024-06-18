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
        if (!self::$db->listCollections(['filter' => ['name' => 'kecsocar']])->isDead()) 
        {
            self::$db->createCollection('kecsocar');
        }
        if (!self::$db->listCollections(['filter' => ['name' => 'kecsocar_images']])->isDead()) 
        {
            self::$db->createCollection('kecsocar_images');
        }
    }
    public static function InsertImage($imageData)
     {
        $collection = self::$db->kecsocar;
        $result = $collection->updateOne
        (
            ['_id' => new \MongoDB\BSON\ObjectId($carId)],
            ['$push' => ['images' => $imageData]]
        );
    
        return $result->getModifiedCount();
     }
    
    public static function InsertCar($car)
    {
        $collection = self::$db->kecsocar;
        return $collection->insertOne($car);
    }
    public static function InsertCarCost($carCost)
    {
    $collection = self::$db->kecsocar;
    $result = $collection->updateOne
    (
        ['_id' => new \MongoDB\BSON\ObjectId($carId)],
        ['$push' => ['carCosts' => $carCost]]
    );

    return $result->getModifiedCount();
    }


    public static function GetCars()
     {
    $collection = self::$db->kecsocar;
    $options = ['sort' => ['license' => 1]];
    $list = $collection->find([], $options);
         
    return $list->toArray();
    }
     public static function GetCarById($id)
    {
    $collection = self::$db->kecsocar;
    $car = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

    return $car;
    }
    public static function GetCarCosts()
    {
    $collection = self::$db->kecsocar;
    $car = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($carId)]);
    return isset($car['carCosts']) ? $car['carCosts'] : [];
    }
    public static function GetImages() 
    {
    $collection = self::$db->kecsocar;
    $car = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($carId)]);
    return isset($car['images']) ? $car['images'] : [];
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