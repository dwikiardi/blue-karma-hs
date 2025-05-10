<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTamuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_tamu', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->char('user_id', LENGTH_ID); // Relasi ke tabel users
            $table->string('email')->unique(); // Kolom email
            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_tamu');
    }
}
