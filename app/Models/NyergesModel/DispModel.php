<?php
namespace App\Models\NyergesModel;

use MongoDB\Client;
use MongoDB\Database;

class DispModel
{
    private static $db;

    public static function Init()
    {
        $uri = 'mongodb://localhost:27017';
        $client = new Client($uri);
        self::$db = $client->selectDatabase('exovizsga');
    }

    public static function InsertDispdata($data)
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergesdisp');
        return $collection->insertOne($data);
    }

    public static function GetDispById($id)
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergesdisp');
        return $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    }

    public static function GetDispData()
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergesdisp');
        $list = $collection->find([], ['projection' => ['name' => 1, 'title' => 1, 'phone' => 1]]);
        return $list->toArray();
    }

    public static function DeleteDispdata($id)
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergesdisp');
        $result = $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return $result->getDeletedCount();
    }

    public static function UpdateDispdata($id, $data)
    {
        self::Init();
        $collection = self::$db->selectCollection('nyergesdisp');
        $result = $collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
        return $result->getModifiedCount();
    }
}
?>