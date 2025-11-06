<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_prime')->default(false)->comment('If true, its a software ownership, false = business ownership company [business ownership is under software ownership]');
            $table->string('hierarchy_level', 50)->nullable()->comment('user context layer - like - primary, secondary, sub-seconday');
            $table->tinyInteger('business_type')->default(1)->comment('1=personal, 2=company');

            $table->foreignId('parent_business_id')
                ->nullable()
                ->constrained('businesses')
                ->onDelete('set null')
                ->comment('Sub-business relationship');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('Business owner → users.id - like user_id');

            $table->string('name', 150)->nullable()->unique()->comment('Name of organization');
            $table->string('slug', 180)->nullable()->unique()->comment('SEO friendly unique key');

            $table->string('email', 120)->nullable()->unique()->comment('Official email of business');
            $table->string('phone2', 20)->nullable()->comment('Secondary contact number');
            $table->text('address')->nullable()->comment('Full address');
            $table->string('website', 150)->nullable()->comment('Business website URL');

            $table->boolean('can_manage_roles')->default(false)->comment('If true, owner can manage employee roles');
            $table->boolean('default_login')->default(false)->comment('If true, its return this business related data after loged in');
            $table->boolean('status')->default(true)->comment('true = enable, false = disable');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
