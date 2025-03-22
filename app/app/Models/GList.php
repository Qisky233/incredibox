<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GList extends Model
{
    use HasFactory;

    // 指定表名
    protected $table = 'list';

    // 可批量赋值的字段
    protected $fillable = [
        'title',
        'desc',
        'info',
        'coverUrl',
        'iframe',
    ];

    // 隐藏字段
    protected $hidden = [
        // 如果有需要隐藏的字段，可以在这里定义
    ];
}
