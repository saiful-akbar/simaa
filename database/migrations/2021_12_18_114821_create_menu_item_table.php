<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_header_id');
            $table->string('nama_menu', 128)->unique();
            $table->string('icon', 64);
            $table->string('href', 200)->unique();
            $table->timestamps();

            $table->foreign('menu_header_id')
                ->references('id')
                ->on('menu_header')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_item');
    }
}