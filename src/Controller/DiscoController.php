<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Disco;
use App\Entity\Artista;

use Symfony\Component\HttpFoundation\Request;
use App\Form\DiscoType;
use App\Form\FiltroType;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class DiscoController extends AbstractController
{
    //#[Route('/disco', name: 'disco')]
    public function index(): Response
    {
        //if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')==null) {
        //    echo 'LOGUEADO';exit;
        //}
        //var_dump('NOOOOOO');exit;
        return $this->render('disco/index.html.twig', [
            'controller_name' => 'DiscoController',
        ]);
    }

    /**
     * @Route("/disco/list", name="disco_list")
     */
    public function list(Request $request)
    {
        $discos = $this->getDoctrine()
            ->getRepository(Disco::class)
            ->findAll();
        $artistas = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->findAll();
        return $this->render('disco/list.html.twig', array(
            'artistas' => $artistas,
            'discos' => $discos,
        ));
    }

    /**
     * @Route("/disco/list1", name="disco_list1")
     */
    public function list1()
    {
        $discos = $this->getDoctrine()
            ->getRepository(Disco::class)
            ->findAll();

        //codi de prova per visualitzar l'array de categoria
         /*dump($discos);
         exit();*/

        return $this->render('disco/list.html.twig', ['discos' => $discos]);
    }

    /**
     * @Route("/disco/search", name="disco_search")
     */
    public function search(Request $request)
    {
       
        //var_dump($request->request->get('seleForm'));exit;
        //recollim el paràmetre 'term' enviat per post
        $seleccion = $request->request->get('seleForm');

        if($seleccion!=0){
            $artistas = $this->getDoctrine()
            ->getRepository(Artista::class)
            ->find($seleccion);

            $discos=[];
            foreach ($artistas->getDiscos() as $key => $value) {
                $disco = new Disco();
                $disco->setId($value->getId());
                $disco->setNombre($value->getNombre());
                $disco->setPrecio($value->getPrecio());
                $disco->setImagen($value->getImagen());
                $disco->setArtista($artistas);
                array_push($discos, $disco);
                //var_dump($disco);
                //var_dump($disco->getId() . $disco->getNombre() . $disco->getPrecio() );
                //var_dump($value->getId() . $value->getNombre() . $value->getPrecio() . $artistas->getNombre());
            }
            //exit;
            //var_dump($artistas->getDiscos()->getNombre());exit;
            // $discos = $this->getDoctrine()
            //     ->getRepository(Disco::class)
            //     ->findLikeArtista($seleccion);//->findLikeNombre($seleccion);

            //codi de prova per visualitzar l'array de categoria
            /*dump($disco);
            exit();*/
        }else{
            $discos=null;
            $artistas = new Artista();
            $artistas->setNombre("");
            $artistas->setApellidos("");
        }
        if($discos != null){
            $OK=true;
        }else $OK=false;
        

        return $this->render('disco/list.html.twig', ['discos' => $discos,'id' => $artistas->getNombre() . " " . $artistas->getApellidos(), 'search' => $OK]);
    }

    /**
     * @Route("/disco/ver/{id<\d+>}", name="disco_ver")
     */
    public function verUnDisco($id)
    {
        $disco = $this->getDoctrine()
            ->getRepository(Disco::class)
            ->find($id);

        //codi de prova per visualitzar l'array de categoria
         /*dump($disco);
         exit();*/
        
        return $this->render('disco/verUnDisco.html.twig', ['disco' => $disco]);
    }

    /**
     * @Route("/disco/delete/{id<\d+>}", name="disco_delete")
     */
    public function delete($id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //LA LINEA ANTERIOR CONTROLA SI ESTA LOGUEADO, SI NO, LO LLEVA A LOGUEARSE
        $disco = $this->getDoctrine()
            ->getRepository(Disco::class)
            ->find($id);
        $nombreImagen = $disco->getImagen();

        $entityManager = $this->getDoctrine()->getManager();
        $nomDisco = $disco->getNombre();
        $entityManager->remove($disco);
        $entityManager->flush();

        //BORRA LA FOTO DE LA CARPETA
        if($nombreImagen != "" && $nombreImagen != null)
        unlink($this->getParameter('carpeta_imagenes_subidas') . '/' . $nombreImagen);

        $this->addFlash(
            'notice',
            'Disc '.$nomDisco.' eliminat!'
        );

        return $this->redirectToRoute('disco_list');
    }

    /**
     * @Route("/disco/edit/{id<\d+>}", name="disco_edit")
     */
    public function edit($id, Request $request, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //LA LINEA ANTERIOR CONTROLA SI ESTA LOGUEADO, SI NO, LO LLEVA A LOGUEARSE

        $disco = $this->getDoctrine()
            ->getRepository(Disco::class)
            ->find($id);

        //fent això el text del boto submit tindria el valor per defecte 'Enviar'
        //$form = $this->createForm(TascaType::class, $tasca);

        //podem personalitzar el text del botó passant una opció 'submit' al builder de la classe DiscoType 
        // http://www.keganv.com/passing-arguments-controller-file-type-symfony-3/
        $form = $this->createForm(DiscoType::class, $disco, array('submit'=>'Desar'));
        
        //també ho podríem fer d'una altra manera: sobreescriure el botó save 
        /*$form = $this->createForm(TascaType::class, $tasca);
        $form->add('save', SubmitType::class, array('label' => 'Desar'));*/

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $nombreImagen = $disco->getImagen();
            $disco = $form->getData();

            /** @var UploadedFile $imagenFile */
            $imagenFile = $form->get('imagen')->getData();

            // esta condición es necesaria porque el campo 'imagen' no es obligatorio,
            // por lo que el archivo debe procesarse solo cuando se carga un archivo
            if ($imagenFile) {

                //$nombreImagen = $disco->getImagen();

                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                // esto es necesario para incluir de forma segura el nombre del archivo como parte de la URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

                // Mueva el archivo al directorio donde se almacenan los folletos
                try {
                    //BORRA LA ANTERIOR FOTO DE LA CARPETA
                    if($nombreImagen != "" && $nombreImagen != null)
                    unlink($this->getParameter('carpeta_imagenes_subidas') . '/' . $nombreImagen);
                    //Mueve la nueva a la carpetacorrecta
                    $imagenFile->move(
                        $this->getParameter('carpeta_imagenes_subidas'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // manejar la excepción si algo sucede durante la carga del archivo
                    echo "ERROR AL MODIFICAR EL ARCHIVO<br><br>";
                    var_dump($e);
                }

                // actualiza la propiedad 'imagen' para almacenar
                // el nombre del archivo PDF en lugar de su contenido
                //$disco->setImagen($newFilename);
                
                //Añado el la ruta de la imagen
                $disco->setImagen($newFilename);
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($disco);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Disco '.$disco->getNombre().' guardat!'
            );
            //message
            return $this->redirectToRoute('disco_list');
        }

        return $this->render('disco/disco.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Editar Disc',
        ));
    }

    /**
     * @Route("/disco/new", name="disco_new")
     */
    public function new(Request $request, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //LA LINEA ANTERIOR CONTROLA SI ESTA LOGUEADO, SI NO, LO LLEVA A LOGUEARSE
        $disco = new Disco();

        //sense la classe SubtascaType faríem:
        /*$form = $this->createFormBuilder($subtasca)
            ->add('tasca', EntityType::class, array('class' => Tasca::class,
            'choice_label' => 'nom'))
            ->add('nom', TextType::class)
            ->add('duracio', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Crear Subtasca'))
            ->getForm();*/

        //podem personalitzar el text passant una opció 'submit' al builder de la classe SubtascaType 
        $form = $this->createForm(DiscoType::class, $disco, array('submit'=>'Crear Disco'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        /** @var UploadedFile $imagenFile */
        $imagenFile = $form->get('imagen')->getData();

        // esta condición es necesaria porque el campo 'imagen' no es obligatorio,
        // por lo que el archivo debe procesarse solo cuando se carga un archivo
        if ($imagenFile) {
            $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
            // esto es necesario para incluir de forma segura el nombre del archivo como parte de la URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

            // Mueva el archivo al directorio donde se almacenan los folletos
            try {
                $imagenFile->move(
                    $this->getParameter('carpeta_imagenes_subidas'),
                    $newFilename
                );
            } catch (FileException $e) {
                // manejar la excepción si algo sucede durante la carga del archivo
                echo "ERROR AL SUBIR EL ARCHIVO<br><br>";
                var_dump($e);
            }

            // actualiza la propiedad 'imagen' para almacenar
            // el nombre del archivo PDF en lugar de su contenido
            //$disco->setImagen($newFilename);
        }else{
            $newFilename=null;
        }

            //CREAR OBJETO DISCO
            $disco = $form->getData();

            //Añado el la ruta de la imagen
            $disco->setImagen($newFilename);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($disco);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Nou disc '.$disco->getNombre().' creat!'
            );

            return $this->redirectToRoute('disco_list');
        }

        return $this->render('disco/disco.html.twig', array(
            'form' => $form->createView(),
            'title' => 'Nou Disc',
        ));
    }
}
