<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:13
 */
namespace Notadd\Page\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Page extends Model {
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'thumb_image',
        'alias',
        'keyword',
        'description',
        'enabled',
    ];
}