<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paket_aksesoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket', 255);
            $table->text('keterangan')->nullable();
            $table->string('minimal_lebar', 150);
            $table->string('maksimal_lebar', 150)->nullable();
            $table->string('minimal_tinggi', 150);
            $table->string('maksimal_tinggi', 150)->nullable();
            $table->integer('jumlah_daun')->nullable();
            $table->boolean('used_status')->default(0);
            $table->bigInteger('created_id')->unsigned();
            $table->foreign('created_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_id')->unsigned()->nullable();
            $table->foreign('updated_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paket_aksesoris');
    }
};
