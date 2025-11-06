<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('cascade');
            $table->string('name')->index(); // system name, not forced unique globally (same role name allowed across businesses)
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable()->comment('config-based permission list');
            $table->boolean('is_system')->default(false)->comment('system/global role (e.g., super_admin)');
            $table->timestamps();

            // unique per business (business_id + name) ensures tenants can reuse role names
            $table->unique(['business_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
