<?php

namespace R64\BatchNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class BatchNotification extends Model
{
    protected $table = 'batch_notifications';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dates = ['dispatch_at', 'created_at', 'updated_at'];
    protected $casts = [
        'dispatched' => 'boolean'
    ];
    protected $fillable = ['notifiable_id', 'notifiable_type', 'notification_class', 'dispatched', 'dispatch_at'];

    /***************************************************************************************
     ** RELATIONS
     ***************************************************************************************/

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function events()
    {
        return $this->hasMany(BatchNotificationEvent::class, 'batch_notification_id');
    }
}
