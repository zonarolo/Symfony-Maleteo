<?php

namespace App\Controller;

use App\Entity\Opinion;
use App\Entity\Usuario;
use App\Form\DemoForm;
use App\Form\OpinionForm;
use App\Form\RegistroForm;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
      $form-> handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $usuario = new Usuario();
        $usuario->setNombre($data['nombre']);
        $usuario->setEmail($data['email']);
        $usuario->setCiudad($data['ciudad']);

        $em->persist($usuario);
        $em->flush();
        return $this->redirectToRoute('enviado');
      }

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
   * @Route("/maleteo/admin/solicitudes", name="solicitudes")
   */
  public function solicitudes(EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Usuario::class);
    $usuarios = $repo->findAll();
    return $this->render('solicitudes.html.twig', ['usuarios'=>$usuarios]);
  }

  /**
   * @Route("/maleteo/admin/opiniones", name="opiniones")
   */
  public function opiniones(EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Opinion::class);
    $opiniones = $repo->findAll();
    return $this->render('opiniones.html.twig', ['opiniones'=>$opiniones]);
  }

  /**
   * @Route("/maleteo/comentar", name="comentar")
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
        return $this->redirectToRoute('opiniones');
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
      $user = $form->getData(); 
      $passwordCifrada = $passwordEncoder->encodePassword($user, $user->getPassword());
      $user->setPassword($passwordCifrada);
      $em->persist($user);
      $em->flush();
      return $this->redirectToRoute('enviado');
    }

    return $this->render('registro.html.twig', ['RegistroForm'=>$form->createView()]);
  }

  /**
   * @Route("/maleteo/admin/solicitudes/{id}/borrar", name="borrarSolicitud")
   */
  public function borrarSolicitud(Usuario $solicitud, EntityManagerInterface $em)
  {
    $em->remove($solicitud);
    $em->flush();
    return new RedirectResponse('/maleteo/admin/solicitudes');
  }

  /**
   * @Route("/maleteo/admin/opiniones/{id}/borrar", name="borrarOpinion")
   */
  public function borrarOpiniones(Opinion $opinion, EntityManagerInterface $em)
  {
    $em->remove($opinion);
    $em->flush();
    return new RedirectResponse('/maleteo/admin/opiniones');
  }

  /**
   * @Route("/base")
   */
  public function base()
  {
    return $this->render('base.html.twig');
  }
}