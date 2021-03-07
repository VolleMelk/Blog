<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Get all posts
     *
     * @return array
     */
    public function index(): array
    {
        $qr = $this->createQueryBuilder('post')->select("post")->orderBy('post.id', 'DESC');

        return $qr->getQuery()->getResult();
    }

    /**
     * Store Post recource to database
     *
     * @param Post $post
     * @param Request $request
     * @return integer
     */
    public function store(User $user, Post $post, Request $request, ObjectManager $entityManager, FormInterface $form): int
    {
        $form->handleRequest($request);
        $post->setCreatedAt(new \DateTime("now"));
        $post->setUpdatedAt(new \DateTime("now"));
        $post->setUserId($user->getId());
        // $post->addUser($user);

        $entityManager->persist($post);

        $entityManager->flush();

        return $post->getId();
    }

    /**
     * Update post recource 
     *
     * @param Post $post
     * @param Request $request
     * @param ObjectManager $entityManager
     * @param FormInterface $form
     * @return integer
     */
    public function update(Post $post, Request $request, ObjectManager $entityManager, FormInterface $form): int
    {
        $form->handleRequest($request);
        $post->setUpdatedAt(new \DateTime("now"));

        $entityManager->flush();

        return $post->getId();
    }


    /**
     * Delete Post recource
     *
     * @param User $user
     * @param Post $post
     * @param ObjectManager $entityManager
     * @return boolean
     */
    public function delete(User $user, Post $post, ObjectManager $entityManager): bool
    {

        if ($user->getId() !== $post->getUserId()) {

            return false;
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return true;
    }
}
