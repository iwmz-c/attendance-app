<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionRequestBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_request_breaks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('correction_request_id')
                ->constrained('correction_requests')
                ->cascadeOnDelete();

            $table->dateTime('break_start_at')->nullable();
            $table->dateTime('break_end_at')->nullable();

            $table->timestamps();

            $table->index('correction_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correction_request_breaks');
    }
}
