<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Linked user');

            $table->foreignId('user_type_id')
                ->constrained('user_types')
                ->onDelete('cascade')
                ->comment('Linked user type');

            $table->foreignId('business_id')
                ->nullable()
                ->constrained('businesses')
                ->onDelete('cascade')
                ->comment('NULL for customer/referral, set for business users');

            $table->boolean('is_primary')->default(false)->comment('Default login panel');
            $table->decimal('commission_rate', 5, 2)->nullable()->comment('Commission rate if applicable');

            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive, 2=suspended');

            $table->unique(['user_id', 'user_type_id', 'business_id'], 'unique_user_business_profiles');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
