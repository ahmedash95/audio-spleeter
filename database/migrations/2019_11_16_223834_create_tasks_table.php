<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedInteger('audio_id')->index();
			$table->string('name');
			$table->integer('exit_code')->nullable();
			$table->longText('script');
			$table->longText('output')->nullable();
			$table->boolean('timed_out')->nullable();
			$table->timestamps();
			$table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
