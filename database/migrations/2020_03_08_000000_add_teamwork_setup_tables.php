<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\FacadesConfig;
use Illuminate\Support\Facades\Schema;

class AddTeamworkSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Config::get('teamwork.team_user_table'), function (Blueprint $table) {
            $allowed_roles = Config::get('teamwork.member_roles');
            array_push($allowed_roles, 'owner');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->enum('role', $allowed_roles)->default($allowed_roles[0]);
            $table->timestamps();
        });

        Schema::table(Config::get('teamwork.team_user_table'), function (Blueprint $table) {
            $table->foreign('user_id')
                ->references(Config::get('teamwork.user_foreign_key'))
                ->on(Config::get('teamwork.users_table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on(Config::get('teamwork.teams_table'))
                ->onDelete('cascade');
        });

        Schema::create(Config::get('teamwork.team_invites_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->text('message')->nullable();
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();
        });

        Schema::table(Config::get('teamwork.team_invites_table'), function (Blueprint $table) {
            $table->foreign('team_id')
                ->references('id')
                ->on(Config::get('teamwork.teams_table'))
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
        Schema::drop(Config::get('teamwork.team_user_table'));
        Schema::drop(Config::get('teamwork.team_invites_table'));
    }
}
