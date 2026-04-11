<?php
// database/migrations/2025_01_01_000000_create_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->index();
            $table->text('address')->nullable();
            $table->string('serial_no')->unique();
            $table->integer('dress_no');
            $table->string('reference_name')->nullable();
            $table->string('reference_phone')->nullable();
            $table->date('booking_date');
            $table->date('delivery_date');
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};