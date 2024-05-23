<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_urls', function (Blueprint $table) {
            $table->id();
            $table->string('id_email');
            $table->text('href');
            $table->integer('harmless');
            $table->integer('malicious');
            $table->integer('suspicious');
            $table->integer('undetected');
            $table->integer('timeout');
            $table->text('self_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_urls');
    }
};
