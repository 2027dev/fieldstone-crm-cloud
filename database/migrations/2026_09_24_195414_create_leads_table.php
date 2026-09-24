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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('web_form_id')->nullable()->index();
            $table->string('title');
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('source')->default('manual');
            $table->string('label')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
