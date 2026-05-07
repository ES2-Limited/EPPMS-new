<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_images', function (Blueprint $table) {
            $table->id();
            $table->string('ulid', 26)->unique();
            $table->foreignId('milestone_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('milestone_ulid', 26)->nullable()->index();
            $table->foreignId('uploader_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('size')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_images');
    }
};
