<?php

use App\Models\Submission;
use App\Models\SubmissionFile;
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
        Schema::create('submission_galleys', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('remote_url')->nullable();
            $table->foreignIdFor(Submission::class)->constrained();
            $table->foreignIdFor(SubmissionFile::class)->nullable()->constrained();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_galleys');
    }
};
