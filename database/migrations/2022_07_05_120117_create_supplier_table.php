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
        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier', 255);
            $table->text('alamat')->nullable();
            $table->string('kota', 150)->nullable();
            $table->string('kode_pos', 50)->nullable();
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
        Schema::dropIfExists('supplier');
    }
};
