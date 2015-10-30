<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:06
 */
namespace Notadd\Article\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Notadd\Category\Models\Category;
class Article extends Model {
    use SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'author',
        'from_author',
        'from_url',
        'content',
        'keyword',
        'description',
        'thumb_image',
        'extend_id',
        'extend_type',
        'user_id',
        'hits',
        'is_sticky',
        'created_at',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category() {
        return $this->belongsTo(Category::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function extend() {
        return $this->morphTo();
    }
    /**
     * @param $value
     * @return string
     */
    public function getDescriptionAttribute($value) {
        if($value == '') {
            return Str::limit(strip_tags($this->attributes['content']), 130, '...');
        }
        return $value;
    }
    /**
     * @return mixed
     */
    public function getNextArticle() {
        $id = $this->where('id', '>', $this->attributes['id'])->min('id');
        return $this->find($id);
    }
    /**
     * @return mixed
     */
    public function getNextArticleInCategory() {
        $id = $this->whereCategoryId($this->attributes['category_id'])->where('id', '>', $this->attributes['id'])->min('id');
        return $this->find($id);
    }
    /**
     * @return mixed
     */
    public function getPreviousArticle() {
        $id = $this->where('id', '<', $this->attributes['id'])->max('id');
        return $this->find($id);
    }
    /**
     * @return mixed
     */
    public function getPreviousArticleInCategory() {
        $id = $this->whereCategoryId($this->attributes['category_id'])->where('id', '<', $this->attributes['id'])->max('id');
        return $this->find($id);
    }
    /**
     * @param $value
     * @return mixed
     */
    public function getThumbImageAttribute($value) {
        if($value == '') {
            $match = array();
            $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $this->attributes['content'], $match);
            if(!empty($match[1])) {
                return $match[1][0];
            }
        } else {
            return $value;
        }
    }
}