<?php

use App\Models\admins;
use App\Models\groups;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(groups::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignIdFor(admins::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');  
            $table->string('name');
            $table->string('phone_number1')->nullable();
            $table->string('phone_number2')->nullable();
            $table->string('location')->nullable();
            $table->string('medical')->nullable();
            $table->float('points')->nullable();
            $table->integer('absens')->nullable();
            $table->enum("level",['sprouts','cubs','scount','advscout']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
