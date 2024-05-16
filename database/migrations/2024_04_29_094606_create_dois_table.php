<?php

use App\Models\Conference;
use App\Models\Enums\DOIStatus;
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
        Schema::create('dois', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class);
            $table->morphs('doiable');
            $table->string('doi')->unique();
            $table->unsignedInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dois');
    }
};
