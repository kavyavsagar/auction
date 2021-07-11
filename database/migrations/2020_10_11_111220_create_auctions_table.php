<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('auctions', function (Blueprint $table) {
            $table->id();            
            $table->string('title');
            $table->enum('bid_type', ['continuous', 'round']);
            $table->string('reference_no')->unique();
            $table->text('description');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('budget')->default(0)->nullable();
            $table->integer('min_step')->default(0);
            $table->integer('start_price')->default(0);
            $table->dateTime('start_time', 0);
            $table->dateTime('end_time', 0);            
            $table->decimal('duration', 5, 2)->default(0);
            $table->dateTime('tendor_start', 0);
            $table->dateTime('tendor_end', 0); 
            $table->integer('winner_bid')->nullable();
            $table->timestamps();
            $table->integer('status')->default(0);
        });

        // Schema::create('auction_files', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('auction_id');
        //     $table->string('file_path');        
        //     $table->timestamps();

        //     $table->foreign('auction_id')
        //         ->references('id')
        //         ->on('auctions')
        //         ->onDelete('cascade');
        // });

        Schema::create('auction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->text('brief');
            $table->integer('quantity')->default(0);
            $table->string('doc_path');        
            $table->timestamps();

            $table->foreign('auction_id')
                ->references('id')
                ->on('auctions')
                ->onDelete('cascade');
        });

        Schema::create('auction_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auction_id');
            $table->integer('user_id')->default(0);        
            $table->string('invite_email');    
            $table->string('token', 20)->unique();    
            $table->timestamps();
            $table->integer('status')->default(0);

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
        Schema::dropIfExists('auctions');
    }
}
