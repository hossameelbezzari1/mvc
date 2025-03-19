<?php

namespace App\Models;

class Visitor extends BaseModel
{
    protected static $table = 'visitors'; // اسم الجدول
    protected static $fillable = ['ip_address', 'last_activity', 'is_active'];
    public $id;

    /**
     * إنشاء أو العثور على زائر بواسطة IP
     */
    public static function findOrCreateByIp($ipAddress)
    {
        $visitor = self::where('ip_address', $ipAddress)[0] ?? null;

        if (!$visitor) {
            $id = self::create([
                'ip_address' => $ipAddress,
                'last_activity' => date('Y-m-d H:i:s')
            ]);
            $visitor = self::find($id);
        }

        return $visitor;
    }

    /**
     * تحديث آخر نشاط للزائر
     */
    public function updateLastActivity()
    {
        self::update($this->id, [
            'last_activity' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ]);
    }
}