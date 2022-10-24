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
        Schema::create('paket_aksesoris_material', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('paket_aksesoris_id')->unsigned();
            $table->foreign('paket_aksesoris_id')->references('id')->on('paket_aksesoris')->onDelete('cascade');
            $table->bigInteger('aksesoris_id')->unsigned();
            $table->foreign('aksesoris_id')->references('id')->on('aksesoris')->onDelete('cascade');
            $table->string('kode', 255);
            $table->string('nama_material', 255);
            $table->json('tipe')->nullable();
            $table->string('warna', 255)->nullable();
            $table->string('satuan', 150);
            $table->integer('qty');
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
        Schema::dropIfExists('paket_aksesoris_material');
    }
};
