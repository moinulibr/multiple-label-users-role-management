<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otp_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who requested OTP');

            $table->string('otp_code', 10)->comment('Generated OTP code');
            $table->dateTime('expires_at')->comment('OTP expiry time');
            $table->string('purpose', 50)->comment('e.g. login, password_reset, mobile_verification, email_verification');
            $table->string('recipient', 50)->nullable()->comment('Number or email OTP sent to');
            $table->timestamp('used_at')->nullable()->comment('When OTP was used');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_attempts');
    }
};
