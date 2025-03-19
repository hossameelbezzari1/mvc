<?php

namespace App\Models;

class Form extends BaseModel
{
    protected static $table = 'forms'; // اسم الجدول
    protected static $fillable = ['visitor_id', 'form_data', 'created_at'];

    /**
     * الحصول على جميع النماذج المرسلة بواسطة زائر معين
     */
    public static function findByVisitorId($visitorId)
    {
        return self::where('visitor_id', $visitorId);
    }
}
