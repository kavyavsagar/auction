<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auction_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->dateTime('startime', 0)->nullable();
            $table->dateTime('endtime', 0)->nullable();
            $table->decimal('duration', 5, 2)->default(0)->nullable();
            $table->integer('order')->default(0);
            $table->integer('allowed_bid')->default(1);
            $table->timestamps();

            $table->foreign('auction_id')
                ->references('id')
                ->on('auctions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auction_times');
    }
}
