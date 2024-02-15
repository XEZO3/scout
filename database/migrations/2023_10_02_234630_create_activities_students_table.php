<?php

use App\Models\activities;
use App\Models\Students;
use App\Models\User;
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
        Schema::create('activities_students', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Students::class)->constrained()->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignIdFor(activities::class)->constrained()->onUpdate('cascade')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities_students');
    }
};
