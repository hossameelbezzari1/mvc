<?php

namespace App\Models;

use PDO;
use App\Support\Collection; // Adjust the namespace to the correct location of the Collection class
use PDOException;

abstract class Model
{
    protected $connection = 'sqlite';
    protected $table;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $with = [];
    protected $withCount = [];
    public $preventsLazyLoading = false;
    protected $perPage = 15;
    public $exists = false;
    public $wasRecentlyCreated = false;
    protected $escapeWhenCastingToString = false;
    protected $attributes = [];
    protected $original = [];
    protected $changes = [];
    protected $casts = [];
    protected $classCastCache = [];
    protected $attributeCastCache = [];
    protected $dateFormat = null;
    protected $appends = [];
    protected $dispatchesEvents = [];
    protected $observables = [];
    protected $relations = [];
    protected $touches = [];
    public $timestamps = true;
    public $usesUniqueIds = false;
    protected $hidden = [];
    protected $visible = [];
    protected $fillable = [];
    protected $guarded = ['*'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    public $forceDeleting = false;

    protected static $pdo;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->original = $attributes;
        if (!self::$pdo) {
            $this->connectDatabase();
        }
    }

    protected function connectDatabase()
    {
        try {
            self::$pdo = new PDO("sqlite:" . __DIR__ . '/../../database/database.sqlite');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        return null;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __toString()
    {
        return (string) json_encode($this->attributes);
    }

    public function getTable()
    {
        return $this->table ?: strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }

    public function update(array $attributes)
    {
        if (!$this->exists) {
            return false;
        }

        $this->changes = array_intersect_key($attributes, array_flip($this->fillable));
        $set = [];
        $params = [];
        $i = 1;

        foreach ($this->changes as $key => $value) {
            $set[] = "$key = :$key";
            $params[":$key"] = $value;
            $i++;
        }

        if ($this->timestamps) {
            $this->changes['updated_at'] = date('Y-m-d H:i:s');
            $set[] = 'updated_at = :updated_at';
            $params[':updated_at'] = $this->changes['updated_at'];
        }

        $params[":id"] = $this->attributes[$this->primaryKey];
        $sql = "UPDATE {$this->getTable()} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = :id";
        $stmt = self::$pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            $this->attributes = array_merge($this->attributes, $this->changes);
            $this->original = $this->attributes;
            $this->changes = [];
        }

        return $result;
    }

    public function delete()
    {
        if (!$this->exists) {
            return false;
        }

        $sql = "DELETE FROM {$this->getTable()} WHERE {$this->primaryKey} = :id";
        $stmt = self::$pdo->prepare($sql);
        $result = $stmt->execute([":id" => $this->attributes[$this->primaryKey]]);

        if ($result) {
            $this->exists = false;
            $this->attributes = [];
        }

        return $result;
    }

    public function with(array $relations)
    {
        $this->with = array_merge($this->with, $relations);
        return $this; // للسلسلة
    }

    public static function get($id)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->getTable()} WHERE {$instance->primaryKey} = :id LIMIT 1";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([":id" => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $instance->exists = true;
            $instance->attributes = $data;
            $instance->original = $data;
        }

        return $instance;
    }

    public static function all()
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->getTable()}";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];

        foreach ($data as $item) {
            $model = new static($item);
            $model->exists = true;
            $models[] = $model;
        }

        return new Collection($models); // تصحيح المساحة الاسمية
    }
}
