<?php
namespace App\Models\KecsoModel;

use MongoDB\Client;
use MongoDB\Database;

class DeposModel
{
    private static $db;

    public static function Init()
    {
        $uri = 'mongodb://localhost:27017';
        $client = new Client($uri);
        self::$db = $client->selectDatabase('exovizsga');
    }

    public static function InsertDepodata($data)
    {
        self::Init(); 
        $collection = self::$db->selectCollection('kecsodepo');
        return $collection->insertOne($data);
    }
    public static function GetDepoById($id)
    {
        self::Init(); 
        $collection = self::$db->selectCollection('kecsodepo');
        return $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    }

    public static function GetDepoData()
    {
        self::Init(); 
        $collection = self::$db->selectCollection('kecsodepo');
        $list = $collection->find([], ['projection' => ['title' => 1, 'content' => 1]]);
        return $list->toArray();
    }

    public static function DeleteDepodata($id)
    {
        self::Init(); 
        $collection = self::$db->selectCollection('kecsodepo');
        $result = $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return $result->getDeletedCount();
    }
    public static function UpdateDepodata($id, $data)
    {
        self::Init();
        $collection = self::$db->selectCollection('kecsodepo');
        $result = $collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
        return $result->getModifiedCount();
    }
}
?>