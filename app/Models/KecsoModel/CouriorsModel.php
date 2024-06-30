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
        $uri = 'mongodb://localhost:27017';
		$client = new \MongoDB\Client($uri);
		
		self::$db = $client->exovizsga;
		self::$db->createCollection('kecsocouriors');
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

	
}