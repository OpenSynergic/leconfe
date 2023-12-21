<?php

use App\Models\Conference;
use App\Models\Enums\PaymentState;
use App\Models\Enums\PaymentType;
use App\Models\User;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->morphs('payable');
            $table->foreignIdFor(User::class)->constrained();
            $table->enum('type', PaymentType::array());
            $table->enum('state', PaymentState::array())->default(PaymentState::Unpaid->value);
            $table->double('amount');
            $table->string('currency_id');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method');
            $table->timestamps();

            $table->index(['type']);
            $table->index(['state']);
        });

        Schema::create('payment_meta', function (Blueprint $table) {
            $table->id();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->longtext('value');

            $table->unique(['metable_type', 'metable_id', 'key']);
            $table->index(['key', 'metable_type']);
        });

        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->string('name');
            $table->enum('type', PaymentType::array());
            $table->text('description')->nullable();
            $table->integer('order_column')->nullable();
            $table->json('fees');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
