<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Logic key: e.g. super_admin, rent_owner, car_owner, driver, referral, customer');
            $table->string('display_name', 100)->nullable()->comment('Display label for UI');
            $table->string('dashboard_key', 50)->nullable()->comment('Example: admin, provider, customer → used to load sidebar/module access');
            $table->boolean('status')->default(true)->comment('true = enable, false = disable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
