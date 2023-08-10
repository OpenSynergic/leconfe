<?php

use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ConferenceType;
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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ConferenceStatus::array())->default(ConferenceStatus::Active->value);
            $table->enum('type', ConferenceType::array())->default(ConferenceType::Offline->value);
            $table->integer('is_current')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
