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
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan', 255);
            $table->text('alamat')->nullable();
            $table->string('nomor_legalitas', 255)->nullable();
            $table->string('no_telp', 150)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->bigInteger('created_id')->unsigned();
            $table->foreign('created_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('updated_id')->unsigned()->nullable();
            $table->foreign('updated_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('perusahaan');
    }
};
