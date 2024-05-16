<?php

use App\Models\User;
use App\Models\Conference;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conference_tabs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->string('title');
            $table->longText('content');
            $table->integer('order');
            $table->boolean('is_shown');
            $table->foreignIdFor(User::class, 'created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_tabs');
    }
};
