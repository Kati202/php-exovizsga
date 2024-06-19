<?php
namespace App\Models;

use App\Requests\Request;
use MongoDB\Client;
use App\Config;
use MongoDB\BSON\ObjectId;

class DeposModel
{
    public static function Init()
    {
        $uri = 'mongodb://localhost:27017';
		$client = new \MongoDB\Client($uri);
		
		//self::$db = $client->exovizsga;
	    //self::$db->createCollection('kecsodepo');
    }
    public static function InsertDepodata($data)
	{
        $collection = self::$db->kecsodepos;
        return $collection->insertOne($data);
	}
    public static function GetCouriors()
    {
        $collection = self::$db->kecsodepo;
        $options = ['ids' => ['courior' => 1]];
        $list = $collection->find([], $options);
        
        return $list->toArray();
   }
    public static function DeleteCouriors($id)
    {
        $collection = self::$db->kecsodepo;
        $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
 
        return $result->getDeletedCount();
    }
}