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
        Schema::create('user_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class);
            $table->string('title');
            // $table->text('short_description')->nullable();
            // $table->text('user_content')->nullable();
            // $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_content_meta', function (Blueprint $table) {
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
        Schema::dropIfExists('user_contents');
    }
};
