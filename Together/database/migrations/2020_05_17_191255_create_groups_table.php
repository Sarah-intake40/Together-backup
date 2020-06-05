<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            
            $table->id();
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
            $table->integer('max_member_number');
            $table->integer('duration');
            $table->string('name')->unique();
            $table->string('description');
            $table->integer('current_number_of_members');
            $table->string('status');
            $table->string('level');
            $table->foreignId('interest_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
