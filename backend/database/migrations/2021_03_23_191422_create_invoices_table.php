<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->enum('type', ['dispatch', 'shipping'])->default('shipping');
            $table->enum('status', ['created', 'received', 'dispatched', 'collected'])->default('created');
            $table->float('weight');
            $table->string('weight_unit')->default('kg');
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->string('total')->nullable();
            $table->float('cost');
            $table->float('insurance');
            $table->json('notes')->nullable();
            $table->boolean('paid')->default(false);
            $table->bigInteger('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('dispatched_at')->nullable();
            $table->dateTime('collected_at')->nullable();
            $table->dateTime('paid_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
