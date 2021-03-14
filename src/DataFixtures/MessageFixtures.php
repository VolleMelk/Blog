<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Message;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class MessageFixtures extends Fixture
{
    protected $user;
    protected $faker;
    protected $postFixtures;

    function __construct()
    {
        $this->faker        = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {

            $this->user = new User();
            $this->user->setUsername($this->faker->name());
            $this->user->setPassword('$argon2i$v=19$m=65536,t=4,p=1$VDh0S1JVZXdPRTdiSEIwMQ$nLKp+Ozemqpewm5YZJrqBTsLpgggtODC6NRaBmQXYkE');
            $this->user->setCreatedAt(new DateTime());
            $this->user->setUpdatedAt(new DateTime());

            $manager->persist($this->user);
            $manager->flush();

            $this->post = new Post();
            $this->post->setDescription($this->faker->name());
            $this->post->setUsers($this->user);
            $this->post->setUsersId($this->user->getId());
            $this->post->setCreatedAt(new DateTime());
            $this->post->setUpdatedAt(new DateTime());

            $manager->persist($this->post);
            $manager->flush();

            $message = new Message();
            $message->setBody($this->faker->sentence());
            $message->setUsers($this->user);
            $message->setUsersId($this->user->getId());
            $message->setPosts($this->post);
            $message->setPostsId($this->post->getId());
            $message->setCreatedAt(new DateTime());
            $message->setUpdatedAt(new DateTime());

            $manager->persist($message);

            $manager->flush();
        }
    }
}
