<?php

use App\Models\Enums\ReviewerConfirmationStatus;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\SubmissionStatusRecommendation;
use App\Models\Participant;
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
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Submission::class)->constrained();
            $table->foreignIdFor(Participant::class)->constrained();
            $table->enum('recommendation', SubmissionStatusRecommendation::array())->nullable();
            $table->enum('confirmation_status', ReviewerConfirmationStatus::array())->default(ReviewerConfirmationStatus::Waiting);
            $table->boolean('canceled')->default(false);
            $table->timestamp('date_assigned')->useCurrent();
            $table->timestamp('date_confirmed')->nullable();
            $table->timestamp('date_completed')->nullable();
            $table->integer('quality')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_assignments');
    }
};
