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
        Schema::create('list', function (Blueprint $table) {
            $table->id(); // 自动创建 `id` 列，等同于 `id` int(10) unsigned NOT NULL AUTO_INCREMENT
            $table->string('title', 255)->nullable(); // 等同于 `title` varchar(255) DEFAULT NULL
            $table->string('desc', 5000)->nullable(); // 等同于 `desc` varchar(5000) DEFAULT NULL
            $table->string('info', 5000)->nullable(); // 等同于 `info` varchar(5000) DEFAULT NULL
            $table->string('coverUrl', 255)->nullable(); // 等同于 `coverUrl` varchar(255) DEFAULT NULL
            $table->string('iframe', 5000)->nullable(); // 等同于 `iframe` varchar(5000) DEFAULT NULL
            $table->timestamps(); // 自动创建 `created_at` 和 `updated_at` 列
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list');
    }
};
