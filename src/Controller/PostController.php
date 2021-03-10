<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Form\PostUpdateFormType;
use App\Repository\PostRepository;
use App\Repository\MessageRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $repository): Response
    {
        $user = $this->security->getUser();

        $posts = $repository->index();

        return $this->render('post/index.html.twig', [
            'user'  => $user,
            'posts' => $posts,
        ]);
    }

    /**
     * Show post resource
     * 
     * @Route("/show/{post}", name="show")
     *
     * @param Post $post
     * @return Response
     */
    public function show(Post $post, MessageRepository $repository): Response
    {
        $user = $this->getUser();

        $messages = $repository->index($post);

        return $this->render('post/show.html.twig', ["post" => $post, "messages" => $messages, 'user' => $user]);
    }

    /**
     * Create resource view
     * 
     * @Route("/create", name="create")
     *
     * @param Request $request
     * @param PostRepository $repository
     * @return void
     */
    public function create(): Response
    {
        $form = $this->createForm(PostFormType::class, new Post());

        return $this->render('post/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Store resource to database
     *
     * @Route("/store", name="store")
     * 
     * @param Request $request
     * @param PostRepository $repository
     * @return void
     */
    public function store(Request $request, PostRepository $repository): Response
    {
        $post = new Post();
        $user = $this->getUser();
        $form = $this->createForm(PostFormType::class, $post);
        $post = $repository->store($user, $post, $request, $this->getDoctrine()->getManager(), $form);

        return $this->redirectToRoute('post.show', ['post' => $post]);
    }

    /**
     * Edit resource page
     * 
     * @Route("/edit/{post}", name="edit")
     *
     * @param Post $post
     * @return Response
     */
    public function edit(Post $post): Response
    {
        $user = $this->getUser();

        if ($user->getId() !== $post->getUsersId()) {
            $this->addFlash('succes', 'You are not autorized to edit post');

            return $this->render('post/index.html.twig');
        }

        $form = $this->createForm(PostUpdateFormType::class, $post);

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Update Post resource
     * 
     * @Route("/update/{post}", name="update")
     *
     * @param Post $post
     * @param Request $request
     * @param PostRepository $repository
     * @return void
     */
    public function update(Post $post, Request $request, PostRepository $repository)
    {
        $user = $this->getUser();

        if ($user->getId() !== $post->getUsersId()) {
            $this->addFlash('succes', 'You are not autorized to edit post');

            return $this->render('post/index.html.twig');
        }

        $form = $this->createForm(PostUpdateFormType::class, $post);

        $post = $repository->update($post, $request, $this->getDoctrine()->getManager(), $form);

        return $this->redirectToRoute("post.edit", ['post' => $post]);
    }

    /**
     * Delete resource from data base
     * 
     * @Route("/delete/{post}", name="delete")
     *
     * @param Post $post
     * @param Request $request
     * @param PostRepository $repository
     * @return void
     */
    public function delete(Post $post, PostRepository $repository): Response
    {
        $user = $this->getUser();
        $postDeleted = $repository->delete($user, $post, $this->getDoctrine()->getManager());

        $postDeleted ? $this->addFlash('success', 'Post removed.') : $this->addFlash('success', 'Post was nod deleted');

        return $this->redirectToRoute("post.index");
    }
}
