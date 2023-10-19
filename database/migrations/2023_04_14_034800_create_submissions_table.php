<?php

use App\Models\Conference;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->boolean('revision_required')->default(false);
            $table->boolean('skipped_review')->default(false);
            $table->enum('stage', SubmissionStage::array())->default(SubmissionStage::Wizard->value);
            $table->enum('status', SubmissionStatus::array())->default(SubmissionStatus::Incomplete->value);
            $table->timestamps();
        });

        Schema::create('submission_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_meta');
        Schema::dropIfExists('submissions');
    }
};
