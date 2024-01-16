<?php

use App\Models\Conference;
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
        Schema::create('plugin_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->string('plugin');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['conference_id', 'plugin', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};
