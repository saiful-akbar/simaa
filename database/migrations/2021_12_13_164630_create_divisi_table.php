<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisiTable extends Migration
{
    /**
     * koneksi database
     */
    protected $connection = 'anggaran';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->create('divisi', function (Blueprint $table) {
                $table->id();
                $table->string('nama_divisi', 128)->unique();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
            ->dropIfExists('divisi');
    }
}
