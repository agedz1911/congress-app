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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('id_product')->unique();
            $table->string('name');
            $table->foreignId('regtype_id')->constrained('registration_types')->cascadeOnDelete();
            $table->decimal('early_bird_idr', 10, 0);
            $table->decimal('early_bird_usd', 10, 0);
            $table->date('early_bird_start');
            $table->date('early_bird_end');
            $table->decimal('regular_idr', 10, 0)->nullable();
            $table->decimal('regular_usd', 10, 0)->nullable();
            $table->date('regular_start')->nullable();
            $table->date('regular_end')->nullable();
            $table->decimal('on_site_idr', 10, 0);
            $table->decimal('on_site_usd', 10, 0);
            $table->date('on_site_start');
            $table->date('on_site_end');
            $table->integer('quota');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_early_bird')->default(false);
            $table->boolean('is_regular')->default(false);
            $table->boolean('is_on_site')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
