<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->boolean('blocked')->default(false);
            $table->string('name');
            $table->string('role')->default(\App\Modules\Users\Interfaces\UsersRoles::SIMPLE->value);
            $table->string('login')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('document')->default('');
            $table->string('CPF')->default('');
            $table->string('image')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->unique()->nullable();
            $table->timestamp('remember_token_at')->nullable();
            $table->string('deleted_reason')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamp('deleted_at', 0)->nullable();
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
        Schema::dropIfExists('users');
    }
};
