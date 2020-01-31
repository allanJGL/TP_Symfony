<?php

namespace App\Controller;

use App\Entity\Videogame;
use App\Form\saveVideogameFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideogameController extends AbstractController
{
    /**
     * @Route("/new/videogame", name="newVideogame")
     */
    public function newVideogame(Request $request): Response
    {
        $video = new Videogame();
        $form = $this->createForm(saveVideogameFormType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('videogame/saveVideogame.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/video/{id}", name="edit")
     */
    public function edit(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
        $entityManager = $this->getDoctrine()->getManager();
        $videogame = $entityManager->getRepository(Videogame::class)->find($id);
        $oldName = $videogame->getTitle();
        $form = $this->createFormBuilder($videogame)
            ->add('title')
            ->add('os')
            ->add('description')
            ->add('release_date', BirthdayType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', $oldName . ' was edited.');

            return $this->redirectToRoute('home');
        }

        return $this->render('videogame/saveVideogame.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Delete a video entity.
     *
     * @Route("/delete/video/{id}", name="delete", methods={"GET","DELETE"})
     *
     */
    public function delete(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
        if (!$id) {
            throw $this->createNotFoundException('No video found');
        }
        $em = $this->getDoctrine()->getManager();
        $videogame = $em->find(Videogame::class, $id);
        $em->remove($videogame);
        $em->flush();
        $this->addFlash('success', $videogame->getTitle() . ' was deleted.');

        return $this->redirectToRoute('home');
    }
}
