<?php

namespace R64\BatchNotifications\Console;

use App\Models\Batch_Notification;
use App\Models\Batch_Notification_Event;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;

class DispatchBatchNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch-notifications:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches Batch Notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notifications = Batch_Notification::where('dispatched', false)
            ->where('dispatch_at', '<=', now()->toDateTimeString())
            ->get();

        /** @var Batch_Notification $notification */
        foreach ($notifications as $notification) {

            $eventables = collect();
            /** @var Batch_Notification_Event $event */
            foreach ($notification->events as $event) {
                $eventables->push($event->eventable);
            }

            /** @var Notifiable $notifiable */
            $notifiable = $notification->notifiable;

            $r = new \ReflectionClass($notification->notification_class);
            $obj = $r->newInstanceArgs([$eventables]);
            $notifiable->notify($obj);

            $notification->dispatched = true;
            $notification->save();
        }
    }
}
