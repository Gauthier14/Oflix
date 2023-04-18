<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $active;

    public function __construct(bool $active)
    {
        $this->active = $active;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->active) {
            $response = $event->getResponse();
            $content = $response->getContent();
            dump($content);

            $newContent = str_replace("<body>", '<body><div class="alert alert-danger">Maintenance prévue mardi 10 janvier à 17h00</div>', $content);

            $response->setContent($newContent);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
