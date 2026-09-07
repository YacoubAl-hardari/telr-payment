<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional local ledger of Telr transactions, populated by your own
 * listener on TelrTransactionAuthorised / TelrTransactionDeclined.
 * The package does not write to this table itself — it only ships the
 * schema so consuming applications have a ready-made starting point.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telr_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tran_ref')->unique();
            $table->string('prev_ref')->nullable();
            $table->string('first_ref')->nullable();
            $table->string('order_ref')->nullable()->index();
            $table->string('cart_id')->index();
            $table->string('type')->nullable();
            $table->string('class')->nullable();
            $table->boolean('is_test')->default(false);
            $table->string('currency', 3);
            $table->decimal('amount', 12, 2);
            $table->string('status', 1)->nullable();
            $table->string('auth_code')->nullable();
            $table->string('auth_message')->nullable();
            $table->string('card_code')->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('bill_email')->nullable();
            $table->json('extra')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telr_transactions');
    }
};
