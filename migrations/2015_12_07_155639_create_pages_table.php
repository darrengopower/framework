<?php
use Notadd\Foundation\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;
class CreatePagesTable extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        $this->schema->create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('thumb_image')->nullable();
            $table->string('alias')->nullable();
            $table->string('keyword')->nullable();
            $table->string('description')->nullable();
            $table->string('template')->nullable();
            $table->text('content')->nullable();
            $table->boolean('enabled')->default(true);
            $table->tinyInteger('order_id')->default(0);
            $table->bigInteger('view_count');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        $this->schema->drop('pages');
    }
}