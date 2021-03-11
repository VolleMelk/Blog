<?php

namespace App\Repository;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Message;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function index(Post $post)
    {
        $messages = $this->findBy(['posts_id' => $post->getId()]);

        return $messages;
    }

    /**
     * Store Post recource to database
     *
     * @param User $user
     * @param Message $message
     * @param Request $request
     * @param ObjectManager $entityManager
     * @return integer
     */
    public function store(User $user, Message $message, Request $request, ObjectManager $entityManager): int
    {
        $post = $entityManager->getRepository(Post::class)->find($request->get('post'));

        $message->setUsersId($user->getId());
        $message->setUsers($user);
        $message->setPostsId((int) $post->getId());
        $message->setBody($request->get('body'));
        $message->setCreatedAt(new DateTime("now"));
        $message->setUpdatedAt(new DateTime("now"));
        $message->setPosts($post);

        $entityManager->persist($message);
        $entityManager->flush();

        return $message->getPostsId();
    }

    /**
     * Update Message recource
     *
     * @param Message $message
     * @param Request $request
     * @param ObjectManager $entityManager
     * @return integer
     */
    public function update(Message $message, Request $request, ObjectManager $entityManager): int
    {
        $message->setBody($request->get('body'));

        $entityManager->flush();

        return $message->getPostsId();
    }

    /**
     * Delete Message recource
     *
     * @param Message $message
     * @param ObjectManager $entityManager
     * @return boolean
     */
    public function delete(Message $message, ObjectManager $entityManager): bool
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return true;
    }
}
