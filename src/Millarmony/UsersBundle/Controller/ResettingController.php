<?php

namespace Millarmony\UsersBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ResettingController extends BaseController
{
    /**
     * Request reset user password: submit form and send email.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (!$user) {
            $request->getSession()->getFlashBag()->add('error', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('fos_user_resetting_request');
        } else {
            /** @var $dispatcher EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            /* Dispatch init event */
            $event = new GetResponseNullableUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $ttl = $this->container->getParameter('fos_user.resetting.retry_ttl');

            if (null !== $user && !$user->isPasswordRequestNonExpired($ttl)) {
                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

                if (null !== $event->getResponse()) {
                    return $event->getResponse();
                }

                if (null === $user->getConfirmationToken()) {
                    /** @var $tokenGenerator TokenGeneratorInterface */
                    $tokenGenerator = $this->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                }

                /* Dispatch confirm event */
                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

                if (null !== $event->getResponse()) {
                    return $event->getResponse();
                }

                $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
                $user->setPasswordRequestedAt(new \DateTime());
                $this->get('fos_user.user_manager')->updateUser($user);

                /* Dispatch completed event */
                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

                if (null !== $event->getResponse()) {
                    return $event->getResponse();
                }
            }

            return new RedirectResponse($this->generateUrl('fos_user_resetting_check_email', array('username' => $username)));
        }
    }
}
