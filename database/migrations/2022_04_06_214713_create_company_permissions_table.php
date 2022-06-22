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
        Schema::create('company_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('display_name');
            $table->string('name');
            $table->text('description');
            $table->enum('type', ['default', 'custom'])->default('default');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_permissions');
    }
};
