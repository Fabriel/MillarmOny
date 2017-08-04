<?php

namespace Millarmony\UsersBundle\Listener;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class RegistrationListener
{

    public function onKernelResponse(FilterUserResponseEvent $event)
    {
        $event->stopPropagation();
    }
}
