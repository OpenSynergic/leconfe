<?php

use App\Models\Serie;
use App\Models\SpeakerRole;
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
        Schema::create('speaker_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Serie::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Serie::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SpeakerRole::class)->constrained()->cascadeOnDelete();
            $table->string('email')->nullable();
            $table->string('given_name');
            $table->string('family_name')->nullable();
            $table->string('public_name')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();

            $table->unique(['email', 'serie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speakers');
        Schema::dropIfExists('speaker_roles');
    }
};
