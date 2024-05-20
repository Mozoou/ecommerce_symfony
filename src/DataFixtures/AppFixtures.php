<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private ?Slugify $slugger = null)
    {
        $this->slugger = new Slugify();
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setUsername('admin')
            ->setEmail('admin@example.com')
            ->addRole(User::ROLE_ADMIN)
        ;
        $password = $this->hasher->hashPassword($user, 'admin');
        $user->setPassword($password);

        $manager->persist($user);

        // Categories
        $categories = [];
        for ($i= 0; $i < 3; $i++) {
            $category = (new Category())->setName('Category '.$i);
            $categories[] = $category;
            $manager->persist($category);
        }

        // Products
        for ($i= 0; $i < 25; $i++) { 
            $product = (new Product())
                ->setName('Product '.$i)
                ->setPrice(rand(300, 1800))
                ->setDescription('Description '. $i)
                ->setCategory($categories[rand(0, 2)]);
            $product->setSlug($this->slugger->slugify($product->getName()));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
