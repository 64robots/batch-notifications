<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('notifiable_id');
            $table->string('notifiable_type');
            $table->string('notification_class');
            $table->boolean('dispatched')->default(false);
            $table->timestamp('dispatch_at');
            $table->timestamps();

            $table->index(['notifiable_id', 'notifiable_type']);
        });

        Schema::create('batch_notification_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('batch_notification_id');
            $table->foreign('batch_notification_id')->references('id')->on('batch_notifications')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('eventable_id');
            $table->string('eventable_type');
            $table->timestamps();

            $table->index(['eventable_id', 'eventable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_notification_events');
        Schema::dropIfExists('batch_notifications');
    }
};
