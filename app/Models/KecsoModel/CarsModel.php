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
    private static $client;
    private static $collectionName = 'kecsocarimages';
 

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

    public static function InsertCarImage($carId, $filename,)
    {
        self::Init();
        $collection = self::$db->selectCollection(self::$collectionName);
    
        try {
            $imageData = [
                'carId' => $carId,
                'filename' => $filename,
                
                
            ];
    
            // Beszúrjuk az adatbázisba csak a fájlnévvel
            $result = $collection->insertOne($imageData);
    
            if ($result->getInsertedCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            // Hibakezelés, ha valami nem sikerülne
            return false;
        }
    }

    public static function GetCarImages($carId)
    {
        self::Init();
        $collection = self::$db->selectCollection(self::$collectionName);
        try {
            $images = $collection->find(['carId' => $carId]);
            return $images;
        } catch (\Throwable $th) {
            // Hibakezelés, ha valami nem sikerülne
            return [];
        }
    }
    public static function DeleteCarImage($carId, $filename,$data)
    {
        self::Init();
        $collection = self::$db->selectCollection(self::$collectionName);
    
        try {
            $result = $collection->deleteOne(['name' => $carId, 'filename' => $filename,'data'=>$data]);
    
            if ($result->getDeletedCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            // Hibakezelés, ha valami nem sikerülne
            return false;
        }
    }



//CarCost   
public static function InsertCarCost($carcost)
{
    self::Init();
    $collection = self::$db->kecsocarcost;

    
    $date = new UTCDateTime(strtotime($carcost['date']) * 1000);

   
    $insertData =
     [
        'ids' => $carcost['ids'],
        'date' => $date,  
        'part' => $carcost['part'],
        'cost' => (int)$carcost['cost'],
    ];

    
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

public static function GetCarCostById($carcostId)
{
    self::Init();
    $collection = self::$db->kecsocarcost;
    $carcost = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectID($carcostId)]);

    // Ellenőrizd a dátum típusát és formázását
    if ($carcost && isset($carcost['date']) && $carcost['date'] instanceof \MongoDB\BSON\UTCDateTime) {
        $carcost['date'] = $carcost['date']->toDateTime()->format('Y-m-d H:i:s');
    }

    return $carcost;
}

public static function UpdateCarCost($carcostId, $carcost)
{
    self::Init();
    $collection = self::$db->kecsocarcost;

    
    $date = new UTCDateTime(strtotime($carcost['date']) * 1000);

  
    $updateData = 
    [
        'ids' => $carcost['ids'],
        'date' => $date,  
        'part' => $carcost['part'],
        'cost' => (int)$carcost['cost'],
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
/*public static function SumCostByDateAndGroup($startDate, $endDate)
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
                '_id' => '$ids',
                'costsum' => ['$sum' => '$cost'],
             ]
        ]
    ];
   
    $result = $collection->aggregate($pipeline)->toArray();
    var_dump($result);
   
    return $result;
}*/
}
?>
