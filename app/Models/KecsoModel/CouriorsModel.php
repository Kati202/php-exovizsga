<?php
namespace App\Models\KecsoModel;

use App\Requests\Request;
use MongoDB\Client;
use App\Config;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

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
    $courior['ids'] = (int)$courior['ids']; // Biztosítjuk, hogy az 'ids' integerré változik

    $collection = self::$db->selectCollection('kecsocouriors');
    
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
    $collection = self::$db->kecsocouriors;
    $cursor = $collection->find([], ['sort' => ['ids' => 1]]);
    return $cursor->toArray();
    }

    public static function GetCouriorById($id)
    {
        self::Init();
        $collection = self::$db->selectCollection('kecsocouriors'); 
        return $collection->findOne(['_id' => new ObjectId($id)]);
    }

    public static function DeleteCouriors($id)
    {
        self::Init();
        $collection = self::$db->selectCollection('kecsocouriors'); 
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
   $collection = self::$db->kecsocouriorsdata;
   return $collection->insertOne($data);
  }
  public static function GetCouriorData()
  {
   self::Init(); 
   $collection = self::$db->kecsocouriorsdata;
   $cursor = $collection->find();
   return $cursor->toArray();
  }
  public static function GetCouriorDataById($id)
  {
      self::Init(); 
      $collection = self::$db->kecsocouriorsdata;
      $filter = self::CreateFilterById($id);
      return $collection->findOne($filter);
  }

  public static function DeleteCouriordata($id)
  {
      self::Init(); 
      $collection = self::$db->kecsocouriorsdata;
      $filter = self::CreateFilterById($id);
      $result = $collection->deleteOne($filter);
      return $result->getDeletedCount();
  }

  public static function UpdateCouriordata($couriorId, $data)
  {
      self::Init(); 
      $collection = self::$db->kecsocouriorsdata;
      $filter = self::CreateFilterById($couriorId);
      $result = $collection->updateOne($filter, ['$set' => $data]);
      return $result->getModifiedCount();
  }
//Futár címei
   public static function InsertAddress($address)
    {
        self::Init();
        $collection = self::$db->kecsoaddresses;

        // Konvertáljuk az időt UTCDateTime objektummá
        $time = new UTCDateTime(strtotime($address['time']) * 1000);

        // A name, ids, day, month stb. mezőket közvetlenül használjuk
        $insertData = [
            'name' => $address['name'],
            'ids' => (int)$address['ids'],
            'day' => (int)$address['day'],
            'month' => $address['month'],
            'time' => $time, // Idő UTCDateTime formátumban
            'total_addresses' => (int)$address['total_addresses'],
            'delivered_addresses' => (int)$address['delivered_addresses'],
            'final_return' => (int)$address['final_return'],
            'live_return' =>(int) $address['live_return']
        ];

        // Beszúrjuk az adatokat az adatbázisba
        $result = $collection->insertOne($insertData);

        return $result;
    }
   public static function GetAddresses()
    {
        self::Init(); 
        $collection = self::$db->kecsoaddresses;
        $cursor = $collection->find();
        return $cursor->toArray();
    }

    public static function GetAddressById($id)
    {
        self::Init(); 
        $collection = self::$db->kecsoaddresses;
        return $collection->findOne(['_id' => new ObjectId($id)]);
    }

    public static function UpdateAddress($addressId, $address)
    {
        self::Init();
        $collection = self::$db->kecsoaddresses;

        // Konvertáljuk az időt UTCDateTime objektummá
        $time = new UTCDateTime(strtotime($address['time']) * 1000);

        // A name, ids, day, month stb. mezőket közvetlenül használjuk
        $updateData = [
            'name' => $address['name'],
            'ids' => (int)$address['ids'],
            'day' => (int)$address['day'],
            'month' => $address['month'],
            'time' => $time, // Idő UTCDateTime formátumban
            'total_addresses' => (int)$address['total_addresses'],
            'delivered_addresses' => (int)$address['delivered_addresses'],
            'final_return' => (int)$address['final_return'],
            'live_return' => (int)$address['live_return']
        ];

        // Az $addressId alapján frissítjük az adatokat az adatbázisban
        $result = $collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectID($addressId)],
            ['$set' => $updateData]
        );

        return $result;
    }

    public static function DeleteAddress($id)
    {
        self::Init(); 
        $collection = self::$db->kecsoaddresses;
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
    }
    public static function SumDeliveredAddressesByDateAndGroup($startDate, $endDate)
    {
        self::Init();

        $collection = self::$db->kecsoaddresses;
        
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
                    'totalDeliveredAddresses' => ['$sum' => ['$toInt' => '$delivered_addresses']],
                    'totalTotalAddresses' => ['$sum' => ['$toInt' => '$total_addresses']]
                ]
            ]
        ];

        $result = $collection->aggregate($pipeline)->toArray();
        var_dump($result);
    }
    /*public static function TestMatch($startDate, $endDate)
{
    self::Init();

    $collection = self::$db->kecsoaddresses;

    $filter = [
        'date' => [
            '$gte' => new UTCDateTime(strtotime($startDate) * 1000),
            '$lte' => new UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000)
        ]
    ];

    $options = [];

    $result = $collection->find($filter, $options)->toArray();
    var_dump($result); // Ellenőrizzük a találatokat

    return $result;
}*/
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
/*<?php
namespace App\Models\KecsoModel;

use App\Requests\Request;
use MongoDB\Client;
use App\Config;
use MongoDB\BSON\ObjectId;

class CouriorsModel
{
    private static $db;

    public static function Init()
    {
        if (self::$db === null) {
            $uri = 'mongodb://localhost:27017';
            $client = new Client($uri);
            self::$db = $client->exovizsga;
            self::$db->createCollection('kecsocouriors'); 
            self::$db->createCollection('kecsocouriorsdata'); 
            self::$db->createCollection('kecsoaddresses'); 
            
        }
    }
    public static function InsertCouriors($courior)
	{
        $collection = self::$db->kecsocouriors;
        return $collection->insertOne($courior);
	}
    public static function GetCouriors()
    {
        $collection = self::$db->kecsocouriors;
        $options = ['ids' => ['courior' => 1]];
        $list = $collection->find([], $options);
        
        return $list->toArray();
   }
   public static function GetCouriorById($id)
   {
        $collection = self::$db->kecsocouriors;
        $courior = $collection->findOne(['_id' => new ObjectId($id)]);

        return $courior;
   }
   public static function DeleteCouriors($id)
   {
       $collection = self::$db->kecsocouriors;
       $result = $collection->deleteOne(['_id' => new ObjectId($id)]);

       return $result->getDeletedCount();
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
  public static function InsertCouriordata($data)
   {
       self::Init(); 
       $collection = self::$db->selectCollection('kecsocourior');
       return $collection->insertOne($data);
   }
  public static function GetCouriorData()
   {
       self::Init(); 
       $collection = self::$db->selectCollection('kecsocourior');
       $list = $collection->find([], ['projection' => ['name' => 1, 'date' => 1,'dateaddress' => 1,'age' => 1,'address' => 1,'mothername' => 1]]);
       return $list->toArray();
   }

   public static function DeleteCouriordata($id)
   {
       self::Init(); 
       $collection = self::$db->selectCollection('kecsocourior');
       $result = $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
       return $result->getDeletedCount();
   }
   public static function UpdateCouriordata($id, $data)
   {
       self::Init();
       $collection = self::$db->selectCollection('kecsocourior');
       $result = $collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
       return $result->getModifiedCount();
   }
   public static function InsertAddress($address)
    {
        self::Init();
        $collection = self::$db->kecsocourior;

    // Ellenőrizzük, hogy a 'month' kulcs létezik-e
        if (!isset($address['month'])) {
        $address['month'] = date('Y-m'); // vagy adjunk hozzá egy megfelelő alapértelmezett értéket, pl. az aktuális hónap
    }

    return $collection->insertOne($address);
    }

    public static function GetAddresses()
    {
        self::Init();
        $collection = self::$db->kecsocourior;
        $list = $collection->find([], ['projection' => ['day' => 1, 'name' => 1, 'time' => 1, 'total_addresses' => 1, 'delivered_addresses' => 1, 'final_return' => 1, 'live_return' => 1]]);
        return $list->toArray();
    }

    public static function GetAddressById($id)
    {
        self::Init();
        $collection = self::$db->kecsocourior;
        $address = $collection->findOne(['_id' => new ObjectId($id)]);

        return $address;
    }

    public static function UpdateAddress($id, $data)
    {
        self::Init();
        $collection = self::$db->kecsocourior;

    // Ellenőrizzük, hogy a 'month' kulcs létezik-e
        if (!isset($data['month'])) {
        $data['month'] = date('Y-m'); // vagy adjunk hozzá egy megfelelő alapértelmezett értéket, pl. az aktuális hónap
    }

    $result = $collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
    return $result->getModifiedCount();
    }

    public static function DeleteAddress($id)
    {
        self::Init();
        $collection = self::$db->kecsocourior;
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
    }
}*/
?>

	