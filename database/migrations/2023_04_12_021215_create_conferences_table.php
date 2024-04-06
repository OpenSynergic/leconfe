<?php

use App\Models\Enums\ConferenceType;
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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('path')->unique();
            $table->enum('type', ConferenceType::array())->default(ConferenceType::Offline->value);
            $table->timestamps();
        });

        Schema::create('conference_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });

        Schema::create('conference_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_meta');
        Schema::dropIfExists('conferences');
    }
};
