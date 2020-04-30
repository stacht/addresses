<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->enum('type', ['primary', 'billing', 'shipping', 'warehouse'])->default('primary');
            $table->string('label')->nullable();
            $table->morphs('addressable');
            $table->string('given_name');
            $table->string('family_name');
            $table->string('organization')->nullable();
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country_code', 2)->index()->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_warehouse')->default(false);
            $table->boolean('is_billing')->default(false);
            $table->boolean('is_shipping')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
