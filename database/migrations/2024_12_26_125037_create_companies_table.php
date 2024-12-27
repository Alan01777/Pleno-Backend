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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('cnpj')->unique();
            $table->string('razao_social')->unique();
            $table->string('nome_fantasia')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('email')->unique();
            $table->enum('size', ['MEI', 'ME', 'EPP', 'EMP', 'EG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
