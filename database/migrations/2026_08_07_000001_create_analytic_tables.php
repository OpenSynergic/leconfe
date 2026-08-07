<?php

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
        Schema::create('analytic_temporary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->unsignedBigInteger('scheduled_conference_id')->nullable();
            $table->unsignedBigInteger('assoc_type');
            $table->unsignedBigInteger('assoc_id');
            $table->unsignedBigInteger('day');
            $table->unsignedBigInteger('entry_time');
            $table->integer('metric')->default(1);
            $table->string('country_id', 2)->nullable();
            $table->string('region', 2)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('load_id', 255);
            $table->smallInteger('file_type')->nullable();
            $table->timestamps();

            $table->index('load_id');
            $table->index(['assoc_type', 'assoc_id']);
        });

        Schema::create('analytic_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('load_id', 255);
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->unsignedBigInteger('scheduled_conference_id')->nullable();
            $table->unsignedBigInteger('proceeding_id')->nullable();
            $table->unsignedBigInteger('submission_id')->nullable();
            $table->unsignedBigInteger('assoc_type');
            $table->unsignedBigInteger('assoc_id');
            $table->string('day', 8);
            $table->string('month', 6);
            $table->smallInteger('file_type')->nullable();
            $table->string('country_id', 2)->nullable();
            $table->string('region', 2)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('metric_type', 255)->default('leconfe::analytic');
            $table->integer('metric')->default(1);
            $table->timestamps();

            $table->index('load_id');
            $table->index(['conference_id', 'scheduled_conference_id']);
            $table->index(['assoc_type', 'assoc_id']);
            $table->index(['submission_id', 'assoc_type', 'month'], 'idx_metrics_sub_assoc_month');
            $table->index(['scheduled_conference_id', 'assoc_type', 'month'], 'idx_metrics_sched_assoc_month');
            $table->index(['day', 'assoc_type'], 'idx_metrics_day_assoc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_metrics');
        Schema::dropIfExists('analytic_temporary_records');
    }
};
