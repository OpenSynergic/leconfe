<?php

use App\Models\Conference;
use App\Models\Participants\Participant;
use App\Models\Participants\ParticipantPosition;
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
        Schema::create('participant_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('participant_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });

        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->unique();
            // $table->foreignIdFor(Conference::class)->constrained();
            // $table->foreignIdFor(ParticipantPosition::class)->constrained();
            // $table->string('type');
            $table->string('given_name');
            $table->string('family_name')->nullable();
            $table->string('public_name')->nullable();
            $table->string('country')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('model_has_participants', function (Blueprint $table) {
            $table->foreignIdFor(Participant::class)->constrained();
            $table->morphs('model');

            $table->primary(['model_id', 'model_type', 'participant_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_meta');
        Schema::dropIfExists('participant_positions');
        Schema::dropIfExists('participants');
    }
};
