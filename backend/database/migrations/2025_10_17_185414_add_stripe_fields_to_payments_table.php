<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeFieldsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Adicionar campo de moeda
            $table->string('currency', 3)->default('brl')->after('amount');
            
            // Adicionar campo de data da transação
            $table->timestamp('transaction_date')->nullable()->after('paid_at');
            
            // Campos específicos do Stripe
            $table->string('stripe_payment_intent_id')->nullable()->after('transaction_id');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_method_id');
            
            // Campo para metadados JSON
            $table->json('metadata')->nullable()->after('stripe_charge_id');
            
            // Índices para melhor performance
            $table->index('stripe_payment_intent_id');
            $table->index('stripe_payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remover índices
            $table->dropIndex(['stripe_payment_intent_id']);
            $table->dropIndex(['stripe_payment_method_id']);
            
            // Remover campos
            $table->dropColumn([
                'currency',
                'transaction_date',
                'stripe_payment_intent_id',
                'stripe_payment_method_id',
                'stripe_charge_id',
                'metadata'
            ]);
        });
    }
}
