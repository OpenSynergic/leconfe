<?php

use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
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
        Schema::create('submission_has_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Submission::class)->constrained();
            $table->foreignIdFor(Participant::class)->constrained();
            $table->foreignIdFor(ParticipantPosition::class)->constrained();
            $table->timestamp('date_assigned')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_has_participants');
    }
};
