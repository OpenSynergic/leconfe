<?php

use App\Models\Conference;
use App\Models\CommitteeRole;
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
        Schema::create('committee_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->unsignedBigInteger('parent_id')->nullable();
            // $table->string('type');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('committee_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });

        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->foreignIdFor(CommitteeRole::class)->constrained();
            $table->string('email')->nullable();
            $table->string('given_name');
            $table->string('family_name')->nullable();
            $table->string('public_name')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();

            $table->unique(['email', 'conference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committees');
    }
};
