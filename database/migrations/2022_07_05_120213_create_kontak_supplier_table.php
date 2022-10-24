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
        Schema::create('kontak_supplier', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
            $table->string('kontak_person', 255);
            $table->string('no_telp', 150);
            $table->string('email', 255)->nullable();
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
        Schema::dropIfExists('kontak_supplier');
    }
};
