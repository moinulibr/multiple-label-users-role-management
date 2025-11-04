<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_login_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable()->comment('like- web or mobile');
            $table->string('platform_key', 100)->nullable()->comment('like- all user, or only customer, or only admin');
            $table->string('platform_hash_key', 100)->nullable()->comment('like- all user, or only customer, or only admin');
            $table->json('login_template_hash_key')->nullable()->comment('login_template_hash_key - its from user_types table'); //json formated
            $table->boolean('status')->default(true)->comment('true = enable, false = disable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_platforms');
    }
};
