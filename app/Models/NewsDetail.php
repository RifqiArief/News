<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'news_detail';

    protected $fillable = [
        'id_news_detail',
        'id_user',
        'id_news',
        'content',
    ];
}
