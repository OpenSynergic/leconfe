<?php

use App\Models\Media;
use App\Models\Review;
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
        Schema::create('reviewer_assigned_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Review::class)->constrained();
            $table->foreignIdFor(Media::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviewer_assigned_files');
    }
};
