<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class CoreV1 extends Migration
{

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create(Config::get('iam.tables.user'), function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('account_id')->default(0)->index();
            $table->string('name', 50);
            $table->string('email', 50);
            $table->string('country', 2);
            $table->string('mobile', 15);
            $table->string('locale', 5)->default('en-GB');
            $table->unsignedTinyInteger('enabled')->default(1);
            $table->timestamps();
            $table->softDeletesV1();
        });

		Schema::create(Config::get('iam.tables.company'), function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('name', 50);
            $table->string('domain', 50);
            $table->unsignedTinyInteger('enabled')->default(1);
            $table->timestamps();
            $table->softDeletesV1();
            $table->authUser();
        });

		Schema::create(Config::get('iam.tables.permission'), function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('name', 100);
            $table->string('title', 100);
            $table->string('description', 100);
        });

		Schema::create(Config::get('iam.tables.role'), function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('company_id')->index()->default(0);
            $table->string('title', 50);
            $table->string('description', 100);
            $table->unsignedTinyInteger('is_default')->default(0)->index();
            $table->unsignedTinyInteger('is_system')->default(0)->index();
            $table->timestamps();
            $table->authUser();
            $table->softDeletesV1();
        });

		Schema::create(Config::get('iam.tables.role_permission'), function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['role_id', 'permission_id']);
        });

		Schema::create(Config::get('iam.tables.user_company'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->string('user_type', 20)->default('user');
            $table->unsignedTinyInteger('active')->default(1)->index();
            $table->unsignedTinyInteger('is_default')->default(0)->index();
            $table->primary(['user_id', 'company_id']);
            $table->timestamps();
        });
        
		Schema::create(Config::get('iam.tables.user_role'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('company_id');
            $table->primary(['user_id', 'role_id', 'company_id']);
        });
    }

    /**
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('iam.tables.user'));
        Schema::dropIfExists(Config::get('iam.tables.company'));
        Schema::dropIfExists(Config::get('iam.tables.permission'));
        Schema::dropIfExists(Config::get('iam.tables.role'));
        Schema::dropIfExists(Config::get('iam.tables.role_permission'));
        Schema::dropIfExists(Config::get('iam.tables.user_company'));
        Schema::dropIfExists(Config::get('iam.tables.user_role'));
    }
}