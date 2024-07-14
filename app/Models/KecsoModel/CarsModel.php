<?php
namespace App\Models\KecsoModel;

use App\Requests\Request;
use MongoDB\Client;
use App\Config;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

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
    
    $existingCar = $collection->findOne(['ids' => $car['ids']]);
    
    if ($existingCar === null) {
        // Ha nincs még ilyen ids, akkor hozzáadjuk az autót
        $result = $collection->insertOne($car);
        return $result;}
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
   

    //$car = $collection->findOne(['_id' => new ObjectId($carId)]);
    //return isset($car['images']) ? $car['images'] : [];
}

private static function isValidObjectId($id)
{
    return preg_match('/^[a-f\d]{24}$/i', $id);
}
//CarCost   
public static function InsertCarCost($carcost)
{
    self::Init();
    $collection = self::$db->kecsocarcost;

    // Konvertáljuk az időt UTCDateTime objektummá
    $date = new UTCDateTime(strtotime($carcost['date']) * 1000);

    // A name, ids, day, month stb. mezőket közvetlenül használjuk
    $insertData =
     [
        'ids' => $carcost['ids'],
        'date' => $date,  
        'part' => $carcost['part'],
        'cost' => $carcost['cost'],
    ];

    // Beszúrjuk az adatokat az adatbázisba
    $result = $collection->insertOne($insertData);

    return $result;
}
public static function GetCarCost()
{
    self::Init(); 
    $collection = self::$db->kecsocarcost;
    $cursor = $collection->find();
    return $cursor->toArray();
}

public static function GetCarCostById($id)
{
    self::Init(); 
    $collection = self::$db->kecsocarcost;
    return $collection->findOne(['_id' => new ObjectId($id)]);
}

public static function UpdateCarCost($carcostId, $carcost)
{
    self::Init();
    $collection = self::$db->kecsocarcost;

    // Konvertáljuk az időt UTCDateTime objektummá
    $date = new UTCDateTime(strtotime($address['date']) * 1000);

    // A name, ids, day, month stb. mezőket közvetlenül használjuk
    $updateData = 
    [
        'ids' => $carcost['ids'],
        'date' => $date,  
        'part' => $carcost['part'],
        'cost' => $carcost['cost'],
    ];

    
    $result = $collection->updateOne(
        ['_id' => new \MongoDB\BSON\ObjectID($carcostId)],
        ['$set' => $updateData]
    );

    return $result;
}

public static function DeleteCarCost($id)
{
    self::Init(); 
    $collection = self::$db->kecsocarcost;
    $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
    return $result->getDeletedCount();
}
public static function SumCostByDateAndGroup($startDate, $endDate)
{
    self::Init();

    $collection = self::$db->kecsocarcost;
    
    $pipeline = [
        [
            '$match' => [
                'time' => [
                    '$gte' => new UTCDateTime(strtotime($startDate) * 1000),
                    '$lte' => new UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000)
                ]
            ]
        ],
        [
            '$group' => [
                '_id' => ['$toInt' => '$ids'],
                'costsum' => ['$sum' => ['$toInt' => '$cost']],
             ]
        ]
    ];

    $result = $collection->aggregate($pipeline)->toArray();
    var_dump($result);
}
}
?>
