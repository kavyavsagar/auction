<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('bid_amount')->default(0);
            $table->string('file_doc')->nullable(); 
            $table->tinyInteger('turn')->default(0);
            $table->timestamps();
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('bids');
    }
}
