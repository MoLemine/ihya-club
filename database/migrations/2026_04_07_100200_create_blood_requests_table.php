<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('patient_name')->nullable();
            $table->string('hospital_name');
            $table->string('city');
            $table->string('urgency_level')->default('normal');
            $table->unsignedInteger('required_units');
            $table->unsignedInteger('fulfilled_units')->default(0);
            $table->text('description');
            $table->string('blood_type')->default('O-');
            $table->string('image_path')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('share_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'urgency_level', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
