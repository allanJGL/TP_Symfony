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
     * @Route("/{idvideo}/edit", name="editVideo")
     */
    public function edit(Request $request, Videogame $video): Response
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
     * Deletes a video entity.
     *
     * @Route("/{idvideo}", name="video_delete")
     * 
     */
    public function delete(Request $request, Videogame $video)
    {
        $form = $this->createDeleteForm($video);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($video);
            $em->flush();
        }
 
        return $this->redirectToRoute('home');
    }

    /**
     * Creates a form to delete a video entity.
     *
     */
    private function createDeleteForm(Videogame $video)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('video_delete', array('idvideo' => $video->getIdvideo())))
            ->getForm()
        ;
    }
}
