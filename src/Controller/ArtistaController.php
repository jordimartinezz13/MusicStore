<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Artista;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArtistaType;

class ArtistaController extends AbstractController
{
    /**
    * ---@Route("/artista", name="categoria")
    */
    public function index(): Response
    {
        return $this->render('artista/index.html.twig', [
            'controller_name' => 'ArtistaController',
        ]);
    }

    /**
     * @Route("/artista/search/{id<\d+>}", name="artista_search")
     */
    public function search($id)
    {
        $artista = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->find($id);

        //codi de prova per visualitzar l'array de categoria
         /*dump($disco);
         exit();*/
        if($artista != null){
            $OK=true;
        }else $OK=false;

        return $this->render('artista/list.html.twig', ['artistas' => $artista,'id' => $id, 'search' => $OK]);
    }

    /**
     * @Route("/artista/list", name="artista_list")
     */
    public function list()
    {
        $artistas = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->findAll();

        //codi de prova per visualitzar l'array de categoria
         /*dump($artistas);
         exit();*/

        return $this->render('artista/list.html.twig', ['artistas' => $artistas]);
    }

    /**
     * @Route("/artista/delete/{id<\d+>}", name="artista_delete")
     */
    public function delete($id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //LA LINEA ANTERIOR CONTROLA SI ESTA LOGUEADO, SI NO, LO LLEVA A LOGUEARSE
        $artista = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->find($id);
        //var_dump(count($artista->getDiscos()));exit;
        if(count($artista->getDiscos()) == 0){
            $entityManager = $this->getDoctrine()->getManager();
            $nomArtista = $artista->getNombre();
            $entityManager->remove($artista);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Artista '.$nomArtista.' eliminat!'
            );

            return $this->redirectToRoute('artista_list');
        } else {
            // echo'<script type="text/javascript">
            // alert("ERROR:\nPrimero debes eliminar todos los discos de este artista.");
            //     history.back();
            // </script>';
            $artistas = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->findAll();

        //codi de prova per visualitzar l'array de categoria
         /*dump($artistas);
         exit();*/

        return $this->render('artista/list.html.twig', ['artistas' => $artistas, 'error' => "Primero debes eliminar todos los discos de este artista."]);
        }
    }

    /**
     * @Route("/artista/edit/{id<\d+>}", name="artista_edit")
     */
    public function edit($id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //LA LINEA ANTERIOR CONTROLA SI ESTA LOGUEADO, SI NO, LO LLEVA A LOGUEARSE
        $artista = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->find($id);

        //fent això el text del boto submit tindria el valor per defecte 'Enviar'
        //$form = $this->createForm(TascaType::class, $tasca);

        //podem personalitzar el text del botó passant una opció 'submit' al builder de la classe ArtistaType 
        // http://www.keganv.com/passing-arguments-controller-file-type-symfony-3/
        $form = $this->createForm(ArtistaType::class, $artista, array('submit'=>'Desar'));
        
        //també ho podríem fer d'una altra manera: sobreescriure el botó save 
        /*$form = $this->createForm(TascaType::class, $tasca);
        $form->add('save', SubmitType::class, array('label' => 'Desar'));*/

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $artista = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($artista);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Artista '.$artista->getNombre().' guardat!'
            );

            return $this->redirectToRoute('artista_list');
        }

        return $this->render('artista/artista.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Editar Artista',
        ));
    }

    /**
     * @Route("/artista/new", name="artista_new")
     */
    public function new(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //LA LINEA ANTERIOR CONTROLA SI ESTA LOGUEADO, SI NO, LO LLEVA A LOGUEARSE
        $artista = new Artista();

        //sense la classe SubtascaType faríem:
        /*$form = $this->createFormBuilder($subtasca)
            ->add('tasca', EntityType::class, array('class' => Tasca::class,
            'choice_label' => 'nom'))
            ->add('nom', TextType::class)
            ->add('duracio', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Crear Subtasca'))
            ->getForm();*/

        //podem personalitzar el text passant una opció 'submit' al builder de la classe SubtascaType 
        $form = $this->createForm(ArtistaType::class, $artista, array('submit'=>'Crear Artista'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $artista = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($artista);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Nou artista '.$artista->getNombre().' creat!'
            );

            return $this->redirectToRoute('artista_list');
        }

        return $this->render('artista/artista.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nou Artista',
        ));
    }
}
