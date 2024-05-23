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
        Schema::create('log_domains', function (Blueprint $table) {
            $table->id();
            $table->string('id_email');
            $table->string('domain');
            $table->integer('malicious');
            $table->integer('harmless');
            $table->integer('suspicious');
            $table->integer('timeout');
            $table->integer('undetected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_domains');
    }
};
