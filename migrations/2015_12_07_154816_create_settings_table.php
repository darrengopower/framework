<?php
use Notadd\Foundation\Database\Schema\Blueprint;
use Notadd\Foundation\Database\Migrations\Migration;
class CreateSettingsTable extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        $this->schema->create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        $this->schema->drop('settings');
    }
}