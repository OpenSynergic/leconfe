<?php

use App\Models\Conference;
use App\Models\Enums\SerieState;
use App\Models\Enums\SerieType;
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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->string('path');
            $table->string('title');
            $table->string('issn')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->enum('type', SerieType::array())->default(SerieType::Offline->value);
            $table->enum('state', SerieState::array())->default(SerieState::Draft->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
