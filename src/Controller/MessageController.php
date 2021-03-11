<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Message;
use App\Form\MessageFormType;
use App\Form\PostUpdateFormType;
use App\Repository\MessageRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/message", name="message.")
 */
class MessageController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * Create resource view
     * 
     * @Route("/create", name="create")
     *
     * @param Request $request
     * @param PostRepository $repository
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(MessageFormType::class, new Message());

        return $this->render('message/create.html.twig', ['form' => $form->createView(), 'post' => $request->query->get('post')]);
    }

    /**
     * Store resource to database
     *
     * @Route("/store", name="store")
     * 
     * @param  Request $request
     * @param  MessageRepository $repository
     * @return Response
     */
    public function store(Request $request, MessageRepository $repository): Response
    {
        $user    = $this->getUser();

        $post    = $repository->store($user, new Message(), $request, $this->getDoctrine()->getManager());

        return $this->redirectToRoute('post.show', ['post' => $post]);
    }

    /**
     * Edit resource page
     * 
     * @Route("/edit/{message}", name="edit")
     *
     * @param Post $post
     * @return Response
     */
    public function edit(Message $message): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(MessageFormType::class, new Message());

        if ($user->getId() !== $message->getUsersId()) {
            $this->addFlash('succes', 'You are not autorized to edit post');

            return $this->render('message/edit.html.twig', ['message' => $message, 'form' => $form->createView()]);
        }


        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Update Post resource
     * 
     * @Route("/update/{message}", name="update")
     *
     * @param Message $message
     * @param Request $request
     * @param MessageRepository $repository
     * @return Response
     */
    public function update(Message $message, Request $request, MessageRepository $repository): Response
    {
        $user = $this->getUser();

        if ((int) $user->getId() !== (int) $message->getUsersId()) {
            $this->addFlash('succes', 'You are not autorized to edit message');

            return $this->redirectToRoute("post.index");
        }

        $post = $repository->update($message, $request, $this->getDoctrine()->getManager());

        return $this->redirectToRoute("post.show", ['post' => $post]);
    }

    /**
     * Delete resource from data base
     * 
     * @Route("/delete/{message}", name="delete")
     *
     * @param Message $message
     * @param PostRepository $repository
     * @return void
     */
    public function delete(Message $message, MessageRepository $repository): Response
    {
        $user = $this->getUser();

        if ((int) $user->getId() === (int) $message->getUsersId() || (int) $user->getId() === (int) $message->getPosts()->getUsersId()) {

            $messageDeleted = $repository->delete($message, $this->getDoctrine()->getManager());

            $messageDeleted ? $this->addFlash('success', 'Message removed.') : $this->addFlash('success', 'Message was nod deleted');

            return $this->redirectToRoute("post.index");
        }

        $this->addFlash('succes', 'You are not autorized to delete message');

        return $this->redirectToRoute("post.index");
    }
}
