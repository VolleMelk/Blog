<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Store recource to database
     *
     * @param User $user
     * @param Request $request
     * @return integer
     */
    public function store(User $user, Request $request, ObjectManager $entityManager, FormInterface $form, UserPasswordEncoderInterface $passwordEncoder): void
    {
        $form->handleRequest($request);
        $data = $form->getData();

        $user->setPassword($passwordEncoder->encodePassword($user, $data->getPassword()));
        $user->setCreatedAt(new \DateTime("now"));
        $user->setUpdatedAt(new \DateTime("now"));

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
