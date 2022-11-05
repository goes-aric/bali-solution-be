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
        Schema::create('material_kaca', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 255);
            $table->string('nama_material', 255);
            $table->string('panjang', 255)->nullable();
            $table->string('lebar', 255)->nullable();
            $table->string('tebal', 255)->nullable();
            $table->string('satuan', 150);
            $table->string('gambar', 255)->nullable();
            $table->decimal('harga_beli_terakhir', 20, 2)->nullable();
            $table->decimal('harga_beli_sebelumnya', 20, 2)->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('material_kaca');
    }
};
