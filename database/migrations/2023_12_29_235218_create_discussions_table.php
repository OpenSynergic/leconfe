<?php

use App\Models\DiscussionTopic;
use App\Models\Enums\SubmissionStage;
use App\Models\Submission;
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

        Schema::create('discussion_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Submission::class)->constrained();
            $table->enum('stage', SubmissionStage::array());
            $table->foreignIdFor(User::class)->constrained();
            $table->boolean('open')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('discussion_topic_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DiscussionTopic::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sent_by')->constrained('users');
            $table->foreignIdFor(DiscussionTopic::class)->constrained();
            $table->longText('message');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
        Schema::dropIfExists('discussion_topic_participants');
        Schema::dropIfExists('discussion_topics');
    }
};
