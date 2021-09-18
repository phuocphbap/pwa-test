<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataTypeInQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topic_questions', function (Blueprint $table) {
            $table->text('title')->change();
        });

        Schema::table('answer_questions', function (Blueprint $table) {
            $table->integer('topic_id')->change();
            $table->text('question')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topic_questions', function (Blueprint $table) {
            $table->string('title');
        });

        Schema::table('answer_questions', function (Blueprint $table) {
            $table->string('topic_id');
            $table->string('question');
        });
    }
}
