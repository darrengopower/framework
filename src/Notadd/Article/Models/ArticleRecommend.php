<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:09
 */
namespace Notadd\Article\Models;
use Notadd\Foundation\Database\Eloquent\Model;
class ArticleRecommend extends Model {
    /**
     * @var array
     */
    protected $fillable = [
        'article_id',
        'position',
    ];
}