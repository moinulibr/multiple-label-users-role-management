<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_profile_id');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');

            $table->unique(['role_id', 'user_profile_id', 'business_id'], 'role_user_profile_unique');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('role_user_profiles');
    }
};
