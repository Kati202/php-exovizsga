<?php
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
        }
    //Főoldal
    }
    public static function InsertCouriors($courior)
	{
        self::Init(); 

        $collection = self::$db->kecsocouriors;
        return $collection->insertOne($courior);
	}
    public static function GetCouriors()
    {
        self::Init(); 
        $collection = self::$db->kecsocouriors;
        $cursor = $collection->find();
        return $cursor->toArray();
   }
   public static function GetCouriorById($id)
   {
    self::Init(); 
    $collection = self::$db->kecsocouriors;
    return $collection->findOne(['_id' => new ObjectId($id)]);
   }
   public static function GetCouriorByIds($ids)
   {
    self::Init(); 
    $collection = self::$db->kecsocouriors;
    return $collection->findOne(['ids' => $ids]);
   }
   public static function DeleteCouriors($id)
   {
       $collection = self::$db->kecsocouriors;
       $result = $collection->deleteOne(['_id' => new ObjectId($id)]);

       return $result->getDeletedCount();
   }
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
        return $collection->insertOne($address);
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

    public static function UpdateAddress($id, $data)
    {
        self::Init(); 
        $collection = self::$db->kecsoaddresses;
        $result = $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
        return $result->getModifiedCount();
    }

    public static function DeleteAddress($id)
    {
        self::Init(); 
        $collection = self::$db->kecsoaddresses;
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount();
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

	