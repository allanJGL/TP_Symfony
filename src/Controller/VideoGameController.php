<?php

namespace App\Controller;

use App\Entity\Videogame;
use App\Form\RegistrationFormVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoGameController extends AbstractController
{
    /**
     * @Route("/registerVideo", name="registerVideo")
     */
    public function register(Request $request): Response
    {
        $video = new Videogame();
        $form = $this->createForm(RegistrationFormVideo::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/registerVideo.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/video/{idvideo}", name="edit", methods={"EDIT", "GET"})
     */
    public function edit(Request $request): Response
    {
        $editForm = $this->createForm('WebBundle\Form\RegistrationFormVideo', $video);
        $editForm->handleRequest($request);
 
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
 
            return $this->redirectToRoute('video_edit', array('idvideo' => $video->getIdvideo()));
        }
 
        return $this->render('video/edit.html.twig', array(
            'video' => $video,
            'edit_form' => $editForm->createView(),
        ));
    }
     /**
     * Delete a video entity.
     *
     * @Route("/delete/video/{idvideo}", name="delete", methods={"GET","DELETE"})
     * 
     */
    public function delete(Request $request)
    {
        $url = $request->server->get('REQUEST_URI');
        $regex = preg_match_all("#[0-9]# ", $url, $id);
        $id = $id[0][0];
        if (!$id || $regex === 0) {
            throw $this->createNotFoundException('No video found');
        }
            $em = $this->getDoctrine()->getManager();
            $video = $em->find(Videogame::class, $id);
            $em->remove($video);
            $em->flush();
        return $this->redirectToRoute('home');
    }
}
