<?php

use App\Models\Conference;
use App\Models\Enums\PaymentState;
use App\Models\Enums\PaymentType;
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
            $table->morphs('model');
            $table->enum('type', PaymentType::array());
            $table->enum('state', PaymentState::array())->default(PaymentState::Pending->value);
            $table->double('amount');
            $table->string('currency_id');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method');
            $table->timestamps();

            $table->index(['type']);
            $table->index(['state']);
        });

        Schema::create('submission_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conference::class)->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order_column')->nullable();
            $table->json('fees');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Schema::create('submission_payment_items_details', function (Blueprint $table){
        //     $table->id();
        //     $table->foreignId('submission_payment_item_id')->constrained('submission_payment_items', indexName:'items_details_item_id_foreign')->cascadeOnDelete();
        //     $table->string('currency_id');
        //     $table->double('fee');
        //     $table->integer('order_column')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
