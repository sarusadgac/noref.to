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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 6)->unique();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('scheme', 10);
            $table->string('host', 255);
            $table->unsignedSmallInteger('port')->nullable();
            $table->text('path')->nullable();
            $table->text('query_string')->nullable();
            $table->text('fragment')->nullable();
            $table->string('url_fingerprint', 64)->unique();
            $table->timestamps();

            $table->index('created_by');
            $table->index('host');
            $table->index('created_at');
            $table->index(['created_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
