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
        Schema::create('ext_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_hash');
            $table->text('device');
            $table->boolean('extStat')->default('1');
            $table->boolean('autoScan')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ext_users');
    }
};
