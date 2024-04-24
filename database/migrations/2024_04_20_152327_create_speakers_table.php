<?php

use App\Models\Serie;
use App\Models\SpeakerRole;
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
        Schema::create('speaker_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Serie::class)->constrained();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('speaker_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });

        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Serie::class)->constrained();
            $table->foreignIdFor(SpeakerRole::class)->constrained();
            $table->string('email')->nullable();
            $table->string('given_name');
            $table->string('family_name')->nullable();
            $table->string('public_name')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();

            $table->unique(['email', 'serie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speakers');
        Schema::dropIfExists('speaker_meta');
        Schema::dropIfExists('speaker_roles');
    }
};
