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
            Schema::create('hotel_rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
                $table->string('room_type');
                $table->decimal('price_idr', 10, 0);
                $table->decimal('price_usd', 10, 0)->nullable();
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->unsignedInteger('quota');
                $table->unsignedInteger('used_count');
                $table->boolean('is_active');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('hotel_rooms');
        }
    };
