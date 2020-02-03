<?php


namespace App\Controller;


use App\Entity\Editor;
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

}