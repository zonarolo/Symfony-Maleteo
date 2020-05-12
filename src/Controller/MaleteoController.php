<?php

namespace App\Controller;

use App\Entity\Login;
use App\Entity\Opinion;
use App\Entity\Registro;
use App\Entity\Usuario;
use App\Form\DemoForm;
use App\Form\LoginForm;
use App\Form\OpinionForm;
use App\Form\RegistroForm;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
   * @Route("/success", name="enviado")
   */
  public function success()
  {
    return $this->render('success.html.twig');
  }

  /**
   * @Route("/failure", name="error")
   */
  public function failure()
  {
    return $this->render('failure.html.twig');
  }
  
  /**
   * @Route("/maleteo/solicitudes", name="solicitudes")
   */
  public function solicitudes(EntityManagerInterface $em)
  {
    $repo = $em->getRepository(Usuario::class);
    $usuarios = $repo->findAll();
    return $this->render('solicitudes.html.twig', ['usuarios'=>$usuarios]);
  }

  /**
   * @Route("/maleteo/opiniones", name="opiniones")
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
   * @Route("/maleteo/login", name="login")
   */
  // public function login(EntityManagerInterface $em, Request $request)
  // {
  //   $form = $this->createForm(LoginForm::class);
  //   $form-> handleRequest($request);
    
  //   if ($form->isSubmitted() && $form->isValid()) {
  //     $data = $form->getData();
  //     $repo= $em->getRepository(Registro::class)
  //               ->findOneBy(
  //                 array('email'=> $data['email'],'password' => $data['password'])
  //               );
      
  //     if ($repo){ 
  //       return $this->redirectToRoute('landing');
  //     } else {
  //       return $this->redirectToRoute('error');
  //     }
  //   }
  //   return $this->render('login.html.twig', ['LoginForm'=>$form->createView()]);
  // }

  /**
   * @Route("/maleteo/registro", name="registro")
   */
  public function register(EntityManagerInterface $em, Request $request)
  {
    $form = $this->createForm(RegistroForm::class);
    $form-> handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()){
      $data = $form->getData();
      $em->persist($data);
      $em->flush();
      return $this->redirectToRoute('enviado');
    }

    return $this->render('registro.html.twig', ['RegistroForm'=>$form->createView()]);
  }
}