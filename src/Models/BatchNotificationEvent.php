<?php

namespace R64\BatchNotifications\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BatchNotificationEvent extends Model
{
    protected $table = 'batch_notification_events';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    /***************************************************************************************
     ** RELATIONS
     ***************************************************************************************/

    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    public function batchNotification(): BelongsTo
    {
        return $this->belongsTo(BatchNotification::class, 'batch_notification_id');
    }

    /***************************************************************************************
     ** CRUD
     ***************************************************************************************/

    public static function queue(Model $notifiable, Model $eventable, string $notification_class, Carbon $dispatch_at): void
    {
        $batch_notification = BatchNotification::firstOrCreate([
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'notification_class' => $notification_class,
            'dispatched' => false
        ], [
            'dispatch_at' => $dispatch_at
        ]);

        $event = new BatchNotificationEvent();
        $event->batchNotification()->associate($batch_notification);
        $event->eventable()->associate($eventable);

        $event->save();
    }
}
