<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');	    
	    
	    
	    
            $table->integer('OwnerID');
            $table->string('OwnerName', 255)->default("");
            $table->string('OwnerURL', 2083);   
            $table->string('OwnerAvatar', 2083);   
	    
            $table->integer('RepoID')->unique();
            $table->string('Name', 255)->default("");
            $table->string('URL', 2083);   
            $table->text('Description')->nullable();
            $table->integer('Stars')->default(0);
            $table->integer('Forks')->default(0);
            $table->integer('Issues')->default(0);
	    
	    $table->dateTime('CreatedDate');
	    $table->dateTime('PushedDate');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
