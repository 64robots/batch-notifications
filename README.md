# Batch Notifications

## Description

Batch Notifications is a Laravel package that groups repetitive notifications in batches.
This package is intended for cases where notifications are dispatched repeatedly for a same Notifiable model.
So, instead of sending lots of notifications (ex.: email messages) repeatedly to the Notifiable model, the
notifications are grouped in batches that will be sent in periods of time.

## Installation

#### 1 - Require the package

``
composer require 64robots/batch-notifications
``

#### 2 - Publish

``
php artisan vendor:publish --provider="R64\BatchNotifications\BatchNotificationsServiceProvider"
``

#### 3 - Run the migration that was just published

``
php artisan migrate
``

## Usage

#### 1 - Create you notification as you normally do, but add the "$eventables" parameter to the constructor:

```
class DocumentAssignedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Collection $eventables */
    private $eventables;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Collection $eventables)
    {
        $this->eventables = $eventables;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->eventables->count() == 1) {
            return (new MailMessage)->view('mail.documents-assigned', [
                'user' => $notifiable,
                'document' => $this->eventables->first(),
            ])->subject("New Document Assigned");
        }

        return (new MailMessage)->view('mail.documents-assigned', [
            'user' => $notifiable,
            'documents' => $this->eventables,
        ])->subject("New Documents Assigned");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
```

#### 2 - Create the notification event for the intended notification:

```
Batch_Notification_Event::makeOne(
    Auth::user(), /* The notifiable model instance. This can be any Notifiable Eloquent model */
    $document, /* The eventable instance you want to attach to the notification. All batch's eventables will be present on your notification constructor). This can be any Eloquent model. */
    DocumentAssignedEmail::class, /* The fully qualified name of your notification class */
    now()->addMinutes(2) /* The minimum interval that the notifiable model will be notified. In this example, the notifications will be sent every two minutes */
);
```

#### 3 - Add the command to your Console/Kernel.php file:


```
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('batch-notifications:dispatch')->everyMinute();
    }

    //...
}
```

#### 4 - "mail.documents-assigned" view example:

```
@extends('layouts.email')

@section('content')
    <p style="color: #444444">
        Hello {{ $user->first_name }},
    </p>

    @if (isset($document))
        <p style="color: #444444">
            A new document has been assigned to you: <a href="{{ url('/documents/' . $document->id) }}">Document #{{ $document->id }}</a>.
        </p>
    @else
        <p style="color: #444444">
            New documents have been assigned to you:<br/><br/>

            @foreach ($documents as $document)
                <a href="{{ url('/documents/' . $document->id) }}">Document #{{ $document->id }}</a><br/>
            @endforeach
        </p>
    @endif
@endsection
```