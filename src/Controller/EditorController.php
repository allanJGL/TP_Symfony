<?php


namespace App\Controller;


use App\Entity\Editor;
use App\Form\EditorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EditorController extends AbstractController
{
    /**
     * @Route("/editors", name="showEditors")
     */
    public function show(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $em = $this->getDoctrine()->getManager()->getRepository(Editor::class);
        $editors = $em->findAll();

        return $this->render('editor/editorsList.html.twig', array(
            'editors' => $editors,
        ));
    }

    /**
     * @Route("/editor/{id}", name="showEditor")
     */
    public function showEditor(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $editor = $entityManager->getRepository(Editor::class)->find($id);

        return $this->render('editor/editorDetails.html.twig', array(
            'name' => $editor->getName(),
            'nationality' => $editor->getNationality(),
            'videogames' => $editor->getVideogames(),
        ));
    }

    /**
     * @Route("/register/editor", name="register")
     */
    public function register(Request $request): Response
    {
        $editor = new Editor();
        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editor);
            $entityManager->flush();
            $this->addFlash('success', 'Editor Created!');
            return $this->redirectToRoute('showEditors');
        }

        return $this->render('editor/saveEditor.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/editor/{id}", name="editEditor")
     */
    public function edit(Request $request, $id): Response
    {
       $entityManager = $this->getDoctrine()->getManager();
        $editor = $entityManager->getRepository(Editor::class)->find($id);
        $oldName = $editor->getName();
        $form = $this->createFormBuilder($editor)
            ->add('name')
            ->add('nationality')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', $oldName . ' was edited.');

            return $this->redirectToRoute('home');
        }

        return $this->render('editor/saveEditor.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Delete a editor entity.
     *
     * @Route("/delete/editor/{id}", name="deleteEditor")
     *
     */
    public function delete(Request $request, $id)
    {
        if (!$id) {
            throw $this->createNotFoundException('No editor found');
        }
        $em = $this->getDoctrine()->getManager();
        $editor = $em->find(Editor::class, $id);
        $em->remove($editor);
        $em->flush();
        $this->addFlash('success', $editor->getName() . ' was deleted.');

        return $this->redirectToRoute('home');
    }

}