<?php

namespace App\Events;

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Publication;

class PublicationPublishersEvent
{
    use Dispatchable, SerializesModels;

    public $publication;
    public $publishers;
    public $action;

    public function __construct(Publication $publication, array $publishers, string $action)
    {
        $this->publication = $publication;
        $this->publishers = $publishers;
        $this->action = $action;
    }
}
