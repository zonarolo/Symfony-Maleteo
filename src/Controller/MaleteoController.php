<?php

namespace App\Controller;

use App\Entity\Opinion;
use App\Entity\Usuario;
use App\Form\DemoForm;
use App\Form\OpinionForm;
use App\Form\RegistroForm;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MaleteoController extends AbstractController
{
  /**
   * @Route("/maleteo", name="landing")
   */
  public function landingpage(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
  {
    try {
      $form = $this->createForm(DemoForm::class);

      $repo= $em->getRepository(Opinion::class);
      $opiniones=$repo->findAll();
      $opinionesRandom=[];
      $num=array_rand($opiniones, 3);
      
      for ($i = 0; $i <= 2; $i++){
        $opinionesRandom[]=$opiniones[$num[$i]];
      }
    } catch (\Throwable $th) {
      $logger->error($th);
    }
    
    return $this->render('landing.html.twig', ['demoForm'=>$form->createView(), 'opiniones'=>$opinionesRandom]);
  }

  /**
   * @Route("/maleteo/success", name="enviado")
   */
  public function success()
  {
    return $this->render('success.html.twig');
  }

  /**
   * @Route("/maleteo/failure", name="error")
   */
  public function failure()
  {
    return $this->render('failure.html.twig');
  }
  
  /**
   * @Route("/maleteo/solicitudes", name="solicitudes")
   * @IsGranted("ROLE_ADMIN")
   */
  public function solicitudes(EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Usuario::class);
    $usuarios = $repo->findAll();
    return $this->render('solicitudes.html.twig', ['usuarios'=>$usuarios]);
  }

  /**
   * @Route("/maleteo/opiniones", name="opiniones")
   * @IsGranted("ROLE_ADMIN")
   */
  public function opiniones(EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Opinion::class);
    $opiniones = $repo->findAll();
    return $this->render('opiniones.html.twig', ['opiniones'=>$opiniones]);
  }

  /**
   * @Route("/maleteo/comentar", name="comentar")
   * @IsGranted("ROLE_USER")
   */
  public function comentar(EntityManagerInterface $em, Request $request, LoggerInterface $logger)
  {
    try {
      $form = $this->createForm(OpinionForm::class);
      $form-> handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();

        $em->persist($data);
        $em->flush();
        return $this->redirectToRoute('landing');
      }
    } catch (\Throwable $th) {
      $logger->error($th);
    }
    
    return $this->render('comentar.html.twig', ['OpinionForm'=>$form->createView()]);
  }

  /**
   * @Route("/registro", name="registro")
   */
  public function register(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
  {
    $form = $this->createForm(RegistroForm::class);
    $form-> handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()){
      try{
        $user = $form->getData(); 
        $passwordCifrada = $passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($passwordCifrada);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('enviado');
      } catch(\Exception $exec){
        $this->addFlash('error', 'Este email ya esta siendo usado.' );
        return $this->redirectToRoute('registro');
      }
      
    }

    return $this->render('registro.html.twig', ['RegistroForm'=>$form->createView()]);
  }

  /**
   * @Route("/maleteo/solicitudes/{id}/borrar", name="borrarSolicitud")
   * @IsGranted("ROLE_ADMIN")
   */
  public function borrarSolicitud(Usuario $solicitud, EntityManagerInterface $em)
  {
    $em->remove($solicitud);
    $em->flush();
    return new RedirectResponse('/maleteo/solicitudes');
  }

  /**
   * @Route("/maleteo/opiniones/{id}/borrar", name="borrarOpinion")
   * @IsGranted("ROLE_ADMIN")
   */
  public function borrarOpiniones(Opinion $opinion, EntityManagerInterface $em)
  {
    $em->remove($opinion);
    $em->flush();
    return new RedirectResponse('/maleteo/opiniones');
  }

  /**
   * @Route("/demo/js/submit", methods={"POST"}, name="demo_submit")
   */
  public function saveDemoWithJS(Request $request, EntityManagerInterface $em)
  {
   
   
    $form= $this-> createForm(DemoForm::class);
    $form->handleRequest($request);
    $datos = json_decode($request->getContent(), true);
    
    
    $demo = new Usuario();
    $demo->setNombre($datos['nombre']);
    $demo->setEmail($datos['email']);
    $demo->setCiudad($datos['ciudad']);
    
    $em->persist($demo);
    $em->flush();

    return new JsonResponse(['msg'=>'Datos enviados correctamente']);
  }

  /**
   * @Route("/maleteo/opiniones/{id}/editar", name="editarOpinion")
   * IsGranted("ROLE_ADMIN")
   */
  public function editarOpiniones(Opinion $opinion, EntityManagerInterface $em, LoggerInterface $logger)
  {
    try {
      $form = $this->createForm(OpinionForm::class,  $opinion);
      // dd($opinion);
      
      if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $opinion->setAutor($data['nombre']);
        $opinion->setCiudad($data['ciudad']);
        $opinion->setComentario($data['comentario']);
        dd($opinion);

        $em->persist($opinion);
        $em->flush();
        return $this->redirectToRoute('opiniones');
      }
    } catch (\Throwable $th) {
      $logger->error($th);
    }
    
    return $this->render('comentar.html.twig', ['OpinionForm'=>$form->createView()]);


  }
}