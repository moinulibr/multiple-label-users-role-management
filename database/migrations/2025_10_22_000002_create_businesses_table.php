<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_prime')->default(false)->comment('If true, its a software ownership');
            $table->tinyInteger('business_type')->default(1)->comment('1=personal, 2=company');

            $table->foreignId('parent_business_id')
                ->nullable()
                ->constrained('businesses')
                ->onDelete('set null')
                ->comment('Sub-business relationship');

            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('Business owner → users.id');

            $table->string('name', 150)->nullable()->unique()->comment('Name of organization');
            $table->string('slug', 180)->nullable()->unique()->comment('SEO friendly unique key');

            $table->string('email', 120)->nullable()->unique()->comment('Official email of business');
            $table->string('phone', 20)->nullable()->comment('Primary contact number');
            $table->string('phone2', 20)->nullable()->comment('Secondary contact number');
            $table->text('address')->nullable()->comment('Full address');
            $table->string('website', 150)->nullable()->comment('Business website URL');

            $table->boolean('can_manage_roles')->default(false)->comment('If true, owner can manage employee roles');
            $table->boolean('status')->default(true)->comment('true = enable, false = disable');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
