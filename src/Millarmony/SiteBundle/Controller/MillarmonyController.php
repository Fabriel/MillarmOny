<?php

namespace Millarmony\SiteBundle\Controller;

use DateTime;
use Millarmony\SiteBundle\Entity\Artists;
use Millarmony\SiteBundle\Entity\Diary;
use Millarmony\SiteBundle\Entity\Music;
use Millarmony\SiteBundle\Entity\Photos;
use Millarmony\SiteBundle\Entity\Videos;
use Millarmony\SiteBundle\Form\Type\ContactType;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class MillarmonyController extends Controller
{
    /**
     * @Route("/", name="millarmony_site_homepage")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(Diary::class);

        $now = new DateTime();
        $date = $now->format('Y-m-d H:i:s');

        $event = $repository->getNext($date);   // Get back the next event

        return $this->render('MillarmonySiteBundle:General:index.html.twig', array('event' => $event));
    }

    /**
     * @Route("/about", name="millarmony_site_about")
     */
    public function aboutAction()
    {
        $repository = $this->getDoctrine()->getRepository(Artists::class);

        $artists = $repository->findAll();      // Retrieves all biographies

        return $this->render('MillarmonySiteBundle:General:about.html.twig', array('artists' => $artists));
    }

    /**
     * @Route("/diary", name="millarmony_site_diary")
     */
    public function diaryAction()
    {
        $repository = $this->getDoctrine()->getRepository(Diary::class);

        $now = new DateTime();
        $date = $now->format('Y-m-d H:i:s');

        $events = $repository->getDiary($date);     // Get back a list of concerts later than $date

        return $this->render('MillarmonySiteBundle:General:diary.html.twig', array('events' => $events));
    }

    /**
     * @Route("/archives", name="millarmony_site_archives")
     */
    public function archivesAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT d FROM MillarmonySiteBundle:Diary d WHERE d.date < CURRENT_DATE() ORDER BY d.date DESC";
        $query = $em->createQuery($dql);                // Query for getting back a list of events previous to a date

        $paginator  = $this->get('knp_paginator');
        $archives = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5                                           // Pagination for archives page (limit by page : 5)
        );

        return $this->render('MillarmonySiteBundle:General:archives.html.twig', array('archives' => $archives));
    }

    /**
     * @Route("/media/music", name="millarmony_site_media_music")
     */
    public function musicAction()
    {
        $repository = $this->getDoctrine()->getRepository(Music::class);

        $musics = $repository->findAll();               // Retrieves all musics

        return $this->render('MillarmonySiteBundle:General:music.html.twig', array('musics' => $musics));
    }

    /**
     * @Route("/media/photos", name="millarmony_site_media_photos")
     */
    public function photosAction()
    {
        $repository = $this->getDoctrine()->getRepository(Photos::class);

        $photos = $repository->findAll();               // Retrieves all photos
        $dates = [];
        foreach ($photos as $photo) {                   // Allows to sort out photos by date
            $date = $photo->getDate();
            if(!in_array($date, $dates))
            {
                $dates[] = $date;
            }
        }
;
        return $this->render('MillarmonySiteBundle:General:photos.html.twig', array(
            'dates'     => $dates,
            'photos'    => $photos
            ));
    }

    /**
     * @Route("/media/videos", name="millarmony_site_media_videos")
     */
    public function videosAction()
    {
        $repository = $this->getDoctrine()->getRepository(Videos::class);

        $videos = $repository->findAll();           // Retrieves all videos

        return $this->render('MillarmonySiteBundle:General:videos.html.twig', array('videos' => $videos));
    }

    /**
     * @Route("/contact", name="millarmony_site_contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->get('form.factory')->create(ContactType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $post = $request->request->get('millarmony_site_contact_form');
            $message = new Swift_Message();
            $message->setSubject('Nouveau message pour le Duo MillarmOny')          // Defines the parameters of the message
                ->setFrom(array('fabrice.loubier@gmail.com' => 'MillarmOny.fr'))
                ->setTo('millarmony@loubier.fr')
                ->setContentType('text/html')
                ->setCharset('utf-8')
                ->setBody(
                    $this->renderView('MillarmonySiteBundle:Email:email.hmtl.twig',
                        array('post' => $post)));
            $this->get('mailer')->send($message);                                   // Send message

            $request->getSession()->getFlashBag()->add('info', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('millarmony_site_homepage');
        }

        return $this->render('MillarmonySiteBundle:General:contact.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/legals", name="millarmony_site_legals")
     */
    public function legalsAction()
    {
        return $this->render('MillarmonySiteBundle:General:legals.html.twig');
    }

}
