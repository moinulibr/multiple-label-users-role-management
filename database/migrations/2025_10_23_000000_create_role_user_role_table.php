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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); //'super_admin'
            $table->string('display_name'); // 'Super Administrator'
            $table->json('permissions')->nullable(); //json formated - array of permissions
            $table->foreignId('business_id')
                ->nullable()
                ->constrained('businesses')
                ->onDelete('cascade')
                ->comment('Links role to a specific business (NULL for system-wide roles)');
            $table->timestamps();
        });


        Schema::create('role_user', function (Blueprint $table) {
            // আইডি প্রয়োজন নেই কারণ এটি একটি পিভট টেবিল

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');

            // 🚨 পরিবর্তন: এটি NULL হতে পারবে, কারণ এটি সিস্টেম রোলের জন্য প্রয়োজন
            $table->foreignId('business_id')
                ->nullable()
                ->constrained('businesses')
                ->onDelete('cascade');

            // 💡 সমাধান: PRIMARY KEY বাদ দিন এবং UNIQUE ইনডেক্স ব্যবহার করুন
            // এটি user_id, role_id, এবং business_id এর সমন্বয়টিকে অনন্য রাখবে
            $table->unique(['user_id', 'role_id', 'business_id'], 'user_role_business_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
