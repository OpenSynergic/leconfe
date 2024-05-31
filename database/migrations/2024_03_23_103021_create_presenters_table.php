<?php

use App\Models\Conference;
use App\Models\Enums\PresenterStatus;
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
        Schema::create('presenters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Submission::class)->constrained();
            $table->string('email')->nullable();
            $table->string('given_name');
            $table->string('family_name')->nullable();
            $table->string('public_name')->nullable();
            $table->string('status')->default(PresenterStatus::Unchecked);
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();

            $table->unique(['email', 'submission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presenters');
    }
};
