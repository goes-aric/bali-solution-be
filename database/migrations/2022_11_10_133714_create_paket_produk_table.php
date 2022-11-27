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
        Schema::create('paket_produk', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kategori_produk_id')->unsigned();
            $table->foreign('kategori_produk_id')->references('id')->on('kategori_produk')->onDelete('cascade');
            $table->string('nama_paket_produk', 255);
            $table->string('warna', 255);
            $table->string('satuan', 255);
            $table->string('gambar', 255)->nullable();
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
        Schema::dropIfExists('paket_produk');
    }
};
