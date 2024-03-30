<?php

use App\Models\AuthorRole;
use App\Models\Conference;
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
        Schema::create('author_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('author_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Submission::class)->constrained();
            $table->foreignIdFor(AuthorRole::class)->constrained();
            $table->string('email')->nullable();
            $table->string('given_name');
            $table->string('family_name')->nullable();
            $table->string('public_name')->nullable();
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
        Schema::dropIfExists('authors');
        Schema::dropIfExists('author_meta');
        Schema::dropIfExists('author_roles');
    }
};
