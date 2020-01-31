<?php

namespace App\Controller;

use App\Entity\Videogame;
use App\Form\RegistrationFormVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilder;

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
    * @Route("/edit/video/{id}", name="editVideo")
    */
    public function edit(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $videoGame = $entityManager->getRepository(Videogame::class)->find($id);
        $form = $this->createFormBuilder($videoGame)
            ->add('title')
            ->add('os')
            ->add('description')
            ->add('release_date', BirthdayType::class)
            ->getForm();
            $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/registerVideo.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $video = $entityManager->getRepository(Videogame::class)->find($id);
    //     if (!$video) {
    //         throw $this->createNotFoundException(
    //             'No video found for id '.$id
    //         );
    //     }
    //     $entityManager->flush();
    //    return $this->redirectToRoute('registerVideo');
    // }
    
     /**
     * Delete a video entity.
     *
     * @Route("/delete/video/{id}", name="delete", methods={"GET","DELETE"})
     * 
     */
    public function delete(Request $request, $id)
    {
        if (!$id) {
            throw $this->createNotFoundException('No video found');
        }
            $em = $this->getDoctrine()->getManager();
            $video = $em->find(Videogame::class, $id);
            $em->remove($video);
            $em->flush();
        return $this->redirectToRoute('home');
    }
}
