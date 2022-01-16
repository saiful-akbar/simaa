<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilTable extends Migration
{
    /**
     * koneksi database
     */
    protected $connection = 'sqlsrv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlsrv')
            ->create('profil', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('avatar', 128)->nullable()->default('avatar_default.webp');
                $table->string('nama_lengkap', 128);
                $table->timestamps();

                // relasi dengan tabel user
                $table->foreign('user_id')
                    ->references('id')
                    ->on('user')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profil');
    }
}
