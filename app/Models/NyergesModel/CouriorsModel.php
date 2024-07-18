<?php
namespace App\Models\NyergesModel;

use App\Requests\Request;
use MongoDB\Client;
use App\Config;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Exception;

class CouriorsModel
{
    private static $db;

    public static function Init()
    {
        if (self::$db === null) {
            $uri = 'mongodb://localhost:27017';
            $client = new Client($uri);
            self::$db = $client->selectDatabase('exovizsga');
        }
    //Főoldal
    }
    public static function InsertCouriors($courior)
   {
    self::Init();
    $courior['ids'] = (int)$courior['ids']; 

    $collection = self::$db->selectCollection('nyergescouriors');
    
    // Ellenőrizzük, hogy van-e már ilyen ids vagy name az adatbázisban
    $existingCourior = $collection->findOne([
        '$or' => [
            ['ids' => $courior['ids']],
            ['name' => $courior['name']]
        ]
    ]);

    if ($existingCourior === null) 
    {
        // Ha nincs még ilyen ids vagy name, akkor hozzáadjuk a futárt
        $result = $collection->insertOne($courior);
        return $result;
    } 
    }

    public static function GetCouriors()
    {
    self::Init();
    $collection = self::$db->nyergescouriors;
    $cursor = $collection->find([], ['sort' => ['ids' => 1]]);
    return $cursor->toArray();
    }

    public static function GetCouriorById($id)
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergescouriors'); 
        return $collection->findOne(['_id' => new ObjectId($id)]);
    }

    public static function DeleteCouriors($id)
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergescouriors'); 
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
    }
   /*public static function GetCouriorByIds($ids)
   {
    self::Init(); 
    $collection = self::$db->kecsocouriors;
    return $collection->findOne(['ids' => $ids]);
   }*/
  
  //Futár személyes adatok 
  public static function InsertCouriordata($data)
  {
    self::Init();

    $collection = self::$db->nyergescouriorsdata;

    // Ellenőrizzük, hogy az ids mező egyedi legyen
    $existingCourior = $collection->findOne([
        'ids' => (int)$data['ids']
    ]);

    if ($existingCourior) {
        throw new Exception('Már létezik ilyen azonosítóval futár.');
    }
    $existingCouriorByName = $collection->findOne([
        'name' => $data['name']
    ]);

    if ($existingCouriorByName) {
        throw new \Exception('Már létezik ilyen névvel futár.');
    }

    // Beszúrás az adatbázisba
    $result = $collection->insertOne($data);

    return $result;
  }
  
  public static function GetCouriorData()
  {
   self::Init(); 
   $collection = self::$db->nyergescouriorsdata;
   $cursor = $collection->find();
   return $cursor->toArray();
  }
  public static function GetCouriorDataById($id)
  {
      self::Init(); 
      $collection = self::$db->nyergescouriorsdata;
      $filter = self::CreateFilterById($id);
      return $collection->findOne($filter);
  }

  public static function DeleteCouriordata($id)
  {
      self::Init(); 
      $collection = self::$db->nyergescouriorsdata;
      $filter = self::CreateFilterById($id);
      $result = $collection->deleteOne($filter);
      return $result->getDeletedCount();
  }

  public static function UpdateCouriordata($couriorId, $data)
  {
      self::Init(); 
      $collection = self::$db->nyergescouriorsdata;
      $filter = self::CreateFilterById($couriorId);
      $result = $collection->updateOne($filter, ['$set' => $data]);
      return $result->getModifiedCount();
  }
//Futár címei
public static function InsertAddress($address)
{
    self::Init();
    $collection = self::$db->nyergesaddresses;

    // Ellenőrizzük, hogy az "ids" mező szám
    if (!is_numeric($address['ids'])) {
        throw new Exception('Az "ids" mező csak szám lehet.');
    }

    // Idő formázása UTCDateTime objektummá
    $time = new UTCDateTime(strtotime($address['time']) * 1000);

    
    $final_return = isset($address['final_return']) ? (int)$address['final_return'] : 0;
    $live_return = isset($address['live_return']) ? (int)$address['live_return'] : 0;

    // Beszúrási adatok összeállítása
    $insertData = [
        'name' => $address['name'],
        'ids' => (int)$address['ids'],
        'day' => (int)$address['day'],
        'month' => $address['month'],
        'time' => $time,
        'total_addresses' => (int)$address['total_addresses'],
        'delivered_addresses' => (int)$address['delivered_addresses'],
        'final_return' => $final_return,
        'live_return' => $live_return
    ];

    // Adatok beszúrása az adatbázisba
    $result = $collection->insertOne($insertData);
    return $result;
  }


   public static function GetAddresses()
    {
        self::Init(); 
        $collection = self::$db->nyergesaddresses;
        $cursor = $collection->find();
        return $cursor->toArray();
    }

    public static function GetAddressById($id)
    {
        self::Init(); 
        $collection = self::$db->nyergesaddresses;
        $address = $collection->findOne(['_id' => new ObjectId($id)]);
    
        // Dátum formázás, ha szükséges
        // Például: $address['date'] helyett a tényleges dátum mező nevét kell itt használni
        if ($address && isset($address['time']) && $address['time'] instanceof \MongoDB\BSON\UTCDateTime) {
            $address['time'] = $address['time']->toDateTime()->format('Y-m-d H:i:s');
        }
    
        return $address;
    }

    public static function UpdateAddress($addressId, $address)
    {
        self::Init();
        $collection = self::$db->nyergesaddresses;

        // Ellenőrzés: ids mező típusa
        if (!is_numeric($address['ids'])) {
            throw new Exception('Az "ids" mező csak szám lehet.');
        }
        $final_return = isset($address['final_return']) ? (int)$address['final_return'] : 0;
        $live_return = isset($address['live_return']) ? (int)$address['live_return'] : 0;

        // További mezők ellenőrzése és adatok frissítése
        $time = new UTCDateTime(strtotime($address['time']) * 1000);
        $updateData = [
            'name' => $address['name'],
            'ids' => (int)$address['ids'],
            'day' => (int)$address['day'],
            'month' => $address['month'],
            'time' => $time,
            'total_addresses' => (int)$address['total_addresses'],
            'delivered_addresses' => (int)$address['delivered_addresses'],
            'final_return' => $final_return,
            'live_return' => $live_return
        ];

        $result = $collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectID($addressId)],
            ['$set' => $updateData]
        );

        return $result;

    }
    

    public static function DeleteAddress($id)
    {
        self::Init(); 
        $collection = self::$db->nyergesaddresses;
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
    }
public static function SumDeliveredAddressesByDateAndGroup($startDate, $endDate)
    {
        self::Init();

        $collection = self::$db->nyergesaddresses;
        
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
                'totalDeliveredAddresses' => ['$sum' => '$delivered_addresses']
                    ]
            ]
        ];

        $result = $collection->aggregate($pipeline)->toArray();
        var_dump($result);
        
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
 