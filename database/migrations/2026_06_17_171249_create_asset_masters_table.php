<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_masters', function (Blueprint $table) {
            $table->id();
 
            $table->string('asset_category', 50);
            $table->string('asset_code', 20);
            $table->string('asset_name', 50);
 
            // Kept nullable because your table screenshot shows NULL allowed.
            // varchar is safer than int for serial numbers that can contain letters.
            $table->string('serial_number', 50)->nullable();
 
            $table->string('brand_name', 50);
 
            // Kept as integer because that is how the existing table is defined.
            // If model numbers can include letters/hyphens, change this to string(50).
            $table->integer('model_number');
 
            $table->string('vendor_id', 50)->nullable();
 
            $table->date('purchase_date')->useCurrent();
            $table->integer('purchase_cost');
 
            $table->enum('status', [
                'available',
                'allocated',
                'under repair',
                'damaged',
                'lost',
                'disposed',
                'reserved',
            ]);
 
            $table->date('warranty_expiry_date')->useCurrent();
 
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('asset_masters');
    }
};