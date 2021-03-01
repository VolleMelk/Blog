<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegsitrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @route("registration", name="registration.")
 */
class RegistrationController extends AbstractController
{
    /**
     * User Registration
     * 
     * @Route("/", name="create")
     *
     * @return Response
     */
    public function create(): Response
    {
        $form = $this->createForm(RegsitrationFormType::class, new User());

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Store new user
     * 
     * @Route("/store", name="store")
     *
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     */
    public function store(Request $request, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegsitrationFormType::class, $user);
        $user = $repository->store($user, $request, $this->getDoctrine()->getManager(), $form, $passwordEncoder);

        return $this->redirectToRoute('app_login');
    }
}
