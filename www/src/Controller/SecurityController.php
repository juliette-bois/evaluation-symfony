<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
  /**
   * @Route("/registration", name="security_registration")
   * @param Request $request
   * @param EntityManagerInterface $manager
   * @param UserPasswordEncoderInterface $encoder
   * @return Response
   */
  public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
  {
    $user = new User();
    $form = $this->createForm(RegistrationType::class, $user);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $hash = $encoder->encodePassword($user, $user->getPassword());
      $user->setPassword($hash);
      $manager->persist($user);
      $manager->flush();

      return $this->redirectToRoute('security_login');
    }

    return $this->render('security/registration.html.twig', [
      'form_registration' => $form->createView(),
    ]);
  }

  /**
   * @Route("/login", name="security_login")
   * @param AuthenticationUtils $authenticationUtils
   * @return Response
   */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
