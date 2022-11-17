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
        Schema::create('paket_produk_aksesoris', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('paket_produk_id')->unsigned();
            $table->foreign('paket_produk_id')->references('id')->on('paket_produk')->onDelete('cascade');
            $table->bigInteger('paket_aksesoris_id')->unsigned();
            $table->foreign('paket_aksesoris_id')->references('id')->on('paket_aksesoris')->onDelete('cascade');
            $table->string('nama_paket', 255);
            $table->string('minimal_lebar', 150)->nullable();
            $table->string('maksimal_lebar', 150)->nullable();
            $table->string('minimal_tinggi', 150)->nullable();
            $table->string('maksimal_tinggi', 150)->nullable();
            $table->integer('jumlah_daun')->nullable();
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
        Schema::dropIfExists('paket_produk_aksesoris');
    }
};
