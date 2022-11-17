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
        Schema::create('paket_produk_material_kaca', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('paket_produk_id')->unsigned();
            $table->foreign('paket_produk_id')->references('id')->on('paket_produk')->onDelete('cascade');
            $table->bigInteger('material_id')->unsigned();
            $table->foreign('material_id')->references('id')->on('material_kaca')->onDelete('cascade');
            $table->string('kode', 255);
            $table->string('nama_material', 255);
            $table->string('panjang', 255)->nullable();
            $table->string('lebar', 255)->nullable();
            $table->string('tebal', 255)->nullable();
            $table->string('satuan', 150)->nullable();
            $table->string('tipe', 255);
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
        Schema::dropIfExists('paket_produk_material_kaca');
    }
};
