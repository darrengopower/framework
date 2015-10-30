<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:16
 */
namespace Notadd\Page;
use Notadd\Page\Models\Page as Model;
class Page {
    private $id;
    protected $data;
    public function __construct($id) {
        $this->id = $id;
        $this->data = Model::find($id);
    }
}