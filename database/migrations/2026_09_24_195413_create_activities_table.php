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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->index();
            $table->string('type')->default('call');
            $table->string('subject');
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('priority')->nullable();
            $table->string('outcome')->nullable();
            $table->text('note')->nullable();
            $table->boolean('done')->default(false);
            $table->timestamp('done_at')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'done', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
