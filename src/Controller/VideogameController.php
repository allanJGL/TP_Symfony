<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Entity\Videogame;
use App\Form\VideogameType;
use App\Repository\EditorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
        $videogame = new Videogame();
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(VideogameType::class, $videogame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($videogame);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('videogame/saveVideogame.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/videogame/{id}", name="edit")
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
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'query_builder' => function (EditorRepository $editors) {
                    return $editors->createQueryBuilder('e')
                        ->orderBy('e.name');
                },
                'choice_label' => 'name',
            ])
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
     * @Route("/delete/videogame/{id}", name="delete", methods={"GET","DELETE"})
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

    /**
     * Show a video entity.
     *
     * @Route("/videogame/{id}", name="show")
     *
     */
    public function show(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $videogame = $em->find(Videogame::class, $id);
        return $this->render('videogame/videogameDetails.html.twig', array(
            'title' => $videogame->getTitle(),
            'os' => $videogame->getOs(),
            'description' => $videogame->getDescription(),
            'release_date' => $videogame->getReleaseDate(),
        ));
    }
}
