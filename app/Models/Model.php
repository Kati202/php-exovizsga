<?php
namespace App\Models;
use App\Requests\Request;
use MongoDB\Client;
use App\Config;
use MongoDB\BSON\ObjectId;

class Model
{     
    private static $client;
    private static $db;

    public static function Init() {
        if (!isset(self::$client)) {
            self::$client = new Client(Config::MONGODB_URI);
        }

        if (!isset(self::$db)) {
            self::$db = self::$client->selectDatabase(Config::DATABASE_NAME);
            self::ensureCollectionsExist();
        }
    }

  
    

    public static function InsertImage($carId,$imageData)
     {
        $collection = self::$db->kecsocar;

        $result = $collection->updateOne
        (
            ['_id' => new ObjectId($carId)],
            ['$push' => ['images' => $imageData]]
        );
    
        return $result->getModifiedCount();
     }
    
    public static function InsertCar($car)
    {
        $collection = self::$db->kecsocar;
        return $collection->insertOne($car);
    }
    public static function InsertCarCost($carId,$carCost)
    {
    $collection = self::$db->kecsocarcost;

    $result = $collection->updateOne
    (
        ['_id' => new ObjectId($carId)],
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
    $car = $collection->findOne(['_id' => new ObjectId($id)]);

    return $car;
    }
    public static function GetCarCosts($carId)
    {
        $collection = self::$db->kecsocarcost;
        $carCosts = $collection->find(['carId' => new ObjectId($carId)]); // Módosítottam az _id helyett carId-re
    
        return $carCosts->toArray();
    }
    public static function GetImages($carId) 
    {
    $collection = self::$db->kecsocar;
    $car = $collection->findOne(['_id' => new ObjectId($carId)]);
    return isset($car['images']) ? $car['images'] : [];
    }

    public static function GetCarCostsByCarId($carId)
    {
    $collection = self::$db->kecsocarcost;
    $carCosts = $collection->find(['carId' => new ObjectId($carId)]);

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
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);

        return $result->getDeletedCount();
    }
    public static function DeleteCarCost($id)
{
    $collection = self::$db->kecsocarcost;

    try {
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
    } catch (\Exception $e) {
        // Hiba kezelése (pl. logolás vagy kivétel dobása)
        return 0;
    }
}
    
    private static function CreateFilterById($id)
    {
        if(!($id instanceof ObjectId))
        {
            $id = new ObjectId($id);
        }
        $filter = ['_id' => $id];
        return $filter;
    }
    private static function ensureCollectionsExist() 
    {
        $collections = ['kecsocar', 'kecsocarcost', 'kecsocar_images'];

    foreach ($collections as $collectionName) {
        $filter = ['name' => $collectionName];
        $options = ['filter' => $filter];

        $collectionInfo = self::$db->listCollections($options);

        // Check if collection exists
        if (empty(iterator_to_array($collectionInfo))) {
            self::$db->createCollection($collectionName);
        }
    }
    }
}
?>