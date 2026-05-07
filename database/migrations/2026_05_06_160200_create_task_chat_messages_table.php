<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('ulid', 26)->unique();
            $table->foreignId('task_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('message');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_chat_messages');
    }
};
