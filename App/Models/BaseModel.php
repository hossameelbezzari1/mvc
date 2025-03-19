<?php

namespace App\Models;

use PDO;
use PDOException;

class BaseModel
{
    protected static $table; // اسم الجدول
    protected static $primaryKey = 'id'; // المفتاح الأساسي
    protected static $fillable = []; // الأعمدة القابلة للتعبئة

    /**
     * الحصول على اتصال بقاعدة البيانات
     */
    protected static function getDb()
    {
        $dbPath = $_ENV['DB_PATH'] ?? __DIR__ . '/../../config/database.sqlite';
        return new PDO('sqlite:' . $dbPath);
    }

    /**
     * إدخال بيانات جديدة
     */
    public static function create(array $data)
    {
        $db = self::getDb();
        $data = self::filterFillable($data); // تصفية البيانات حسب $fillable
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO " . static::$table . " ($columns) VALUES ($values)";
        $stmt = $db->prepare($query);

        try {
            $stmt->execute($data);
            return $db->lastInsertId();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * تحديث بيانات موجودة
     */
    public static function update($id, array $data)
    {
        $db = self::getDb();
        $data = self::filterFillable($data); // تصفية البيانات حسب $fillable
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(', ', $set);

        $query = "UPDATE " . static::$table . " SET $set WHERE " . static::$primaryKey . " = :id";
        $stmt = $db->prepare($query);
        $data['id'] = $id;

        try {
            $stmt->execute($data);
            return true;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * حذف بيانات
     */
    public static function delete($id)
    {
        $db = self::getDb();
        $query = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id";
        $stmt = $db->prepare($query);

        try {
            $stmt->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * الحصول على جميع السجلات
     */
    public static function all()
    {
        $db = self::getDb();
        $query = "SELECT * FROM " . static::$table;
        $stmt = $db->prepare($query);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * الحصول على سجل واحد بواسطة المفتاح الأساسي
     */
    public static function find($id)
    {
        $db = self::getDb();
        $query = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id";
        $stmt = $db->prepare($query);

        try {
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * البحث بواسطة عمود معين
     */
    public static function where($column, $value)
    {
        $db = self::getDb();
        $query = "SELECT * FROM " . static::$table . " WHERE $column = :value";
        $stmt = $db->prepare($query);

        try {
            $stmt->execute(['value' => $value]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * تصفية البيانات حسب $fillable
     */
    protected static function filterFillable(array $data)
    {
        return array_intersect_key($data, array_flip(static::$fillable));
    }
}
