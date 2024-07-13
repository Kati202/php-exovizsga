<?php
namespace App\Models\KecsoModel;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use App\Config;

class CarsModel
{
    private static $db;

    public static function Init()
    {
        if (self::$db === null) 
        {
            $uri = 'mongodb://localhost:27017';
            $client = new Client($uri);
            self::$db = $client->exovizsga;
        }
    }

    

    public static function InsertCar($car)
    {
        self::Init();
        $collection = self::$db->kecsocar;
        $result = $collection->insertOne($car);
        return $result->getInsertedId(); // Visszatérési érték lehet az újonnan beszúrt dokumentum _id-je
    }

    public static function GetCars()
    {
        self::Init();
        $collection = self::$db->kecsocar;
        $cursor = $collection->find();
        return $cursor->toArray();
    }

    public static function GetCarById($id)
    {
        self::Init();
        $collection = self::$db->kecsocar;
        return $collection->findOne(['_id' => new ObjectId($id)]);
    }

    public static function DeleteCar($id)
    {
        self::Init();
        $collection = self::$db->kecsocar;
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
    }

    public static function InsertCarImage($carId, $imageData)
    {
        self::Init();
        $collection = self::$db->kecsocar_images;
    
        // Az adatok beszúrása az adatbázisba ObjectId használatával
        $result = $collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($carId)],
            ['$push' => ['images' => $imageData]]
        );
    
        return $result->getModifiedCount();;
    }
    
    public static function GetCarImages($carId)
{
    self::Init();
    $collection = self::$db->kecsocar_images;

    // Validate the carId
    if (!self::isValidObjectId($carId)) {
        throw new InvalidArgumentException("Invalid ObjectId string: $carId");
    }
    var_dump($carId);

    $car = $collection->findOne(['_id' => new ObjectId($carId)]);
    return isset($car['images']) ? $car['images'] : [];
}

private static function isValidObjectId($id)
{
    return preg_match('/^[a-f\d]{24}$/i', $id);
}
   
    public static function InsertCarCost($carCost)
    {
    self::Init();
    $collection = self::$db->kecsocar_cost;

    // Konvertáljuk a dátumot UTCDateTime formátumba
    $timestamp = strtotime($carCost['date']);
    $milliseconds = $timestamp * 1000;
    $carCost['date'] = new \MongoDB\BSON\UTCDateTime($milliseconds);

    $result = $collection->insertOne($carCost);
    return $result->getInsertedCount() > 0;
    }

    public static function GetCarCosts($carId)
    {
        self::Init();
        $collection = self::$db->kecsocar_cost;
        $carCosts = $collection->find(['_id' => new ObjectId($carId)]);
    
        $costs = [];
        foreach ($carCosts as $cost) {
            $costs[] = 
            [
                '_id' => (string) $cost['_id'],
                'date' => $cost['date']->toDateTime()->format('Y-m-d H:i:s'),  
                'part' => $cost['part'],
                'price' => $cost['price']
            ];
        }
        var_dump($costs);
    
        return $costs;
    }

    public static function UpdateCarCost($costId, $carCost)
    {
        self::Init();
        $collection = self::$db->kecsocar_cost;
    
        // Konvertáljuk a dátumot UTCDateTime formátumba
        $timestamp = strtotime($carCost['date']);
        $milliseconds = $timestamp * 1000;
        $carCost['date'] = new \MongoDB\BSON\UTCDateTime($milliseconds);
    
        $result = $collection->updateOne(
            ['_id' => new ObjectId($costId)],
            ['$set' => [
                'date' => $carCost['date'],
                'part' => $carCost['part'],
                'price' => $carCost['price']
            ]]
        );
        return $result->getModifiedCount();
    }

    public static function DeleteCarCost($costId)
    {
        self::Init();
        $collection = self::$db->kecsocar_cost;
        $result = $collection->deleteOne(['_id' => new ObjectId($costId)]);
        return $result->getDeletedCount();
    }
}
?>
