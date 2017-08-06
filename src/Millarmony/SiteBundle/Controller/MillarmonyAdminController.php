<?php

namespace Millarmony\SiteBundle\Controller;

use Millarmony\SiteBundle\Entity\Artists;
use Millarmony\SiteBundle\Entity\Diary;
use Millarmony\SiteBundle\Entity\Music;
use Millarmony\SiteBundle\Entity\Photos;
use Millarmony\SiteBundle\Entity\Videos;
use Millarmony\SiteBundle\Form\Type\ArtistsType;
use Millarmony\SiteBundle\Form\Type\DiaryType;
use Millarmony\SiteBundle\Form\Type\EditMusicType;
use Millarmony\SiteBundle\Form\Type\EditPhotoType;
use Millarmony\SiteBundle\Form\Type\EditVideoType;
use Millarmony\SiteBundle\Form\Type\MusicType;
use Millarmony\SiteBundle\Form\Type\PhotosType;
use Millarmony\SiteBundle\Form\Type\VideosType;
use Millarmony\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class MillarmonyAdminController extends Controller
{
    /**
     * @Route("/admin/manage", name="millarmony_admin_manage")
     */
    public function adminAction(Request $request)
    {
        $repoArtists = $this->getDoctrine()->getRepository(Artists::class);
        $artists = $repoArtists->findAll();                                     // Retrieves all biographies

        $repoDiary = $this->getDoctrine()->getRepository(Diary::class);
        $events = $repoDiary->findBy(array(), array('date' => 'DESC'));         // Retrieves events ordered by descending date

        $repoMusic = $this->getDoctrine()->getRepository(Music::class);
        $musics = $repoMusic->findAll();                                        // Retrieves all musics

        $repoPhotos = $this->getDoctrine()->getRepository(Photos::class);
        $photos = $repoPhotos->findBy(array(), array('date'  => 'DESC'));       // Retrieves photos ordered by descending date

        $repoVideos = $this->getDoctrine()->getRepository(Videos::class);
        $videos = $repoVideos->findAll();                                       // Retrieves all videos

        $repoUsers = $this->getDoctrine()->getRepository(User::class);
        $users = $repoUsers->findAll();                                         // Retrieves all users


        return $this->render('MillarmonySiteBundle:Admin:admin.html.twig', array(
            'artists' => $artists,
            'events'  => $events,
            'musics'  => $musics,
            'photos'  => $photos,
            'videos'  => $videos,
            'users'   => $users
        ));
    }

    /**
     * @Route("/admin/artist/{id}", name="millarmony_admin_artist")
     */
    public function artistAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Artists::class);

        $artist = $repository->find($id);                                   // Retrieves biography by id to display it

        return $this->render('MillarmonySiteBundle:Admin:artist.html.twig', array('artist' => $artist));
    }

    /**
     * @Route("/admin/event/{id}", name="millarmony_admin_event")
     */
    public function eventAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Diary::class);

        $event = $repository->find($id);                                    // Retrieves event by id to display it

        return $this->render('MillarmonySiteBundle:Admin:event.html.twig', array('event' => $event));
    }

    /**
     * @Route("/admin/addBio", name="millarmony_admin_addBio")
     */
    public function addBioAction(Request $request)
    {
        $artist = new Artists();                                                    // Creates a new biography
        $form = $this->get('form.factory')->create(ArtistsType::class, $artist);    // Creates the form for the new biography

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($artist);
            $em->flush();                                                           // Registers the new biography in the DB

            $request->getSession()->getFlashBag()->add('info', 'La biographie a bien été enregistrée.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addBio.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nouvelle fiche artiste'
        ));
    }

    /**
     * @Route("/admin/editBio/{id}", name="millarmony_admin_editBio")
     */
    public function editBioAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Artists::class);
        $artist = $repository->find($id);                                           // Retrieves biography by id

        $form = $this->get('form.factory')->create(ArtistsType::class, $artist);    // Creates the form for edit the biography

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($artist);
            $em->flush();                                                           // Registers modifications in the DB

            $request->getSession()->getFlashBag()->add('info', 'La biographie a bien été modifiée.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addBio.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Modification fiche artiste',
            'artist' => $artist
        ));
    }

    /**
     * @Route("/admin/deleteBio/{id}", name="millarmony_admin_deleteBio")
     */
    public function deleteBioAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $artist = $em->getRepository(Artists::class)->find($id);            // Retrieves biography by id

        $em->remove($artist);
        $em->flush();                                                       // Removes biography from DB

        $request->getSession()->getFlashBag()->add('info', 'La biographie a bien été supprimée.');
        return $this->redirectToRoute('millarmony_admin_manage');
    }

    /**
     * @Route("/admin/addEvent", name="millarmony_admin_addEvent")
     */
    public function addEventAction(Request $request)
    {
        $event = new Diary();                                                   // Creates a new event
        $form = $this->get('form.factory')->create(DiaryType::class, $event);   // Creates the form for the new event

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();                                                       // Registers the new event in the DB

            $request->getSession()->getFlashBag()->add('info', "L'évènement a bien été enregistré.");
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addEvent.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nouvel évènement'
        ));
    }

    /**
     * @Route("/admin/editEvent/{id}", name="millarmony_admin_editEvent")
     */
    public function editEventAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Diary::class);
        $event = $repository->find($id);                                        // Retrieves event by id

        $form = $this->get('form.factory')->create(DiaryType::class, $event);   // Creates the form for edit the event

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();                                                       // Registers modifications in the DB

            $request->getSession()->getFlashBag()->add('info', "L'évènement a bien été modifié.");
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addEvent.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Modification évènement',
            'event' => $event
        ));
    }

    /**
     * @Route("/admin/deleteEvent/{id}", name="millarmony_admin_deleteEvent")
     */
    public function deleteEventAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository(Diary::class)->find($id);               // Retrieves event by id

        $em->remove($event);
        $em->flush();                                                       // Removes event from DB
        
        $request->getSession()->getFlashBag()->add('info', "L'évènement a bien été supprimé.");
        return $this->redirectToRoute('millarmony_admin_manage');
    }


    /**
     * @Route("/admin/addMusic", name="millarmony_admin_addMusic")
     */
    public function addMusicAction(Request $request)
    {
        $music = new Music();                                                   // Creates a new music
        $form = $this->get('form.factory')->create(MusicType::class, $music);   // Creates the form for the new music

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($music);
            $em->flush();                                                       // Registers the new music in the DB

            $request->getSession()->getFlashBag()->add('info', 'Le fichier a bien été enregistré.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addMusic.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nouveau fichier son'
        ));
    }

    /**
     * @Route("/admin/editMusic/{id}", name="millarmony_admin_editMusic")
     */
    public function editMusicAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Music::class);
        $music = $repository->find($id);                                            // Retrieves music by id

        $form = $this->get('form.factory')->create(EditMusicType::class, $music);   // Creates the form for edit the music

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($music);
            $em->flush();                                                           // Registers modifications of music in the DB

            $request->getSession()->getFlashBag()->add('info', 'Le fichier a bien été modifié.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:editMusic.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Modification fichier son',
            'Music' => $music
        ));
    }

    /**
     * @Route("/admin/deleteMusic/{id}", name="millarmony_admin_deleteMusic")
     */
    public function deleteMusicAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $music = $em->getRepository(Music::class)->find($id);                   // Retrieves music by id

        $em->remove($music);
        $em->flush();                                                           // Removes music from DB

        $request->getSession()->getFlashBag()->add('info', 'Le fichier son a bien été supprimé.');
        return $this->redirectToRoute('millarmony_admin_manage');
    }

    /**
     * @Route("/admin/addPhoto", name="millarmony_admin_addPhoto")
     */
    public function addPhotoAction(Request $request)
    {
        $photo = new Photos();                                                  // Creates a new photo
        $form = $this->get('form.factory')->create(PhotosType::class, $photo);  // Creates the form for this new photo

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->flush();                                                       // Registers the new photo in the DB

            $request->getSession()->getFlashBag()->add('info', 'La photo et sa miniature ont bien été enregistrées.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addPhoto.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nouvelle photo'
        ));
    }

    /**
     * @Route("/admin/editPhoto/{id}", name="millarmony_admin_editPhoto")
     */
    public function editPhotoAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Photos::class);
        $photo = $repository->find($id);                                            // Retrieves photo by id

        $form = $this->get('form.factory')->create(EditPhotoType::class, $photo);   // Creates the form for edit the photo
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->flush();                                                           // Registers modifications of photo in the DB

            $request->getSession()->getFlashBag()->add('info', 'Les informations de la photo ont bien été modifiées.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:editPhoto.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Modification informations photo',
            'photo' => $photo
        ));
    }

    /**
     * @Route("/admin/deletePhoto/{id}", name="millarmony_admin_deletePhoto")
     */
    public function deletePhotoAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $photo = $em->getRepository(Photos::class)->find($id);                      // Retrieves photo by id

        $em->remove($photo);
        $em->flush();                                                               // Removes photo from DB

        $request->getSession()->getFlashBag()->add('info', 'La photo et sa miniature ont bien été supprimées.');
        return $this->redirectToRoute('millarmony_admin_manage');
    }



    /**
     * @Route("/admin/addVideo", name="millarmony_admin_addVideo")
     */
    public function addVideoAction(Request $request)
    {
        $video = new Videos();                                                  // Creates a new video
        $form = $this->get('form.factory')->create(VideosType::class, $video);  // Creates the form for the new video

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();                                                       // Registers the new video in the DB

            $request->getSession()->getFlashBag()->add('info', 'La vidéo a bien été enregistrée.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:addVideo.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nouvelle vidéo'
        ));
    }

    /**
     * @Route("/admin/editVideo/{id}", name="millarmony_admin_editVideo")
     */
    public function editVideoAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Videos::class);
        $video = $repository->find($id);                                            // Retrieves video by id

        $form = $this->get('form.factory')->create(EditVideoType::class, $video);   // Creates the form for edit the video

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();                                                           // Registers modifications of video in the DB

            $request->getSession()->getFlashBag()->add('info', 'Le titre de la vidéo a bien été modifié.');
            return $this->redirectToRoute('millarmony_admin_manage');
        }

        return $this->render('MillarmonySiteBundle:Admin:editVideo.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Modification vidéo',
            'video' => $video
        ));
    }

    /**
     * @Route("/admin/deleteVideo/{id}", name="millarmony_admin_deleteVideo")
     */
    public function deleteVideoAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $video = $em->getRepository(Videos::class)->find($id);                      // Retrieves video by id

        $em->remove($video);
        $em->flush();                                                               // Removes video from DB

        $request->getSession()->getFlashBag()->add('info', 'La vidéo a bien été supprimée.');
        return $this->redirectToRoute('millarmony_admin_manage');
    }

    /**
     * @Route("/admin/deleteUser/{id}", name="millarmony_admin_deleteUser")
     */
    public function deleteUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($id);                         // Retrieves user by id

        $em->remove($user);
        $em->flush();                                                               // Removes user from DB

        $request->getSession()->getFlashBag()->add('info', "Le membre a bien été supprimé.");
        return $this->redirectToRoute('millarmony_admin_manage');
    }

}
