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
        Schema::table('meta', function (Blueprint $table) {
            if (!Schema::hasColumn('meta', 'numeric_value')) {
                $table->decimal('numeric_value', 65, 30)->nullable()->index()->after('value');
            }
            if (!Schema::hasColumn('meta', 'hmac')) {
                $table->string('hmac', 64)->nullable()->after('numeric_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta', function (Blueprint $table) {
            $table->dropColumn(['numeric_value', 'hmac']);
        });
    }
};
