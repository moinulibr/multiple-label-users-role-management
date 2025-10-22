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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)->nullable();
            $table->string('email', 120)->nullable()->unique();
            $table->string('password')->nullable();

            $table->string('mobile', 20)->unique()->comment('Primary mobile for login');
            $table->string('secondary_mobile', 20)->nullable();

            $table->string('provider', 30)->nullable()->comment('e.g. google, facebook, apple');
            $table->string('provider_id', 100)->nullable()->comment('OAuth provider user id');

            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive, 2=suspended');
            $table->boolean('is_developer')->default(false)->comment('Developer bypass login access');

            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            //$table->string('otp_code', 10)->nullable()->comment('Temporary OTP code for verification');

            $table->string('profile_picture')->nullable();

            $table->timestamps();
        });
        
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
