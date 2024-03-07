<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager): void
    {

        // COMMERCIAUX
        $usersData = [
            ['name' => 'simon', 'email' => 'simoncharbonnier03@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'john', 'email' => 'johndoe@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'shaun', 'email' => 'shaunwinter@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'martina', 'email' => 'martinasnow@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'jacques', 'email' => 'jaques@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'jean', 'email' => 'jean@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'karim', 'email' => 'karim@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'stephane', 'email' => 'stephane@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'boris', 'email' => 'boris@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'karine', 'email' => 'karine@gmail.com', 'roles' => ["ROLE_USER"]],
        ];

        foreach ($usersData as $data) {
            $customer = new Customer();
            $customer->setName($data['name']);
            $customer->setEmail($data['email']);
            $customer->setPassword($this->hasher->hashPassword($customer, 'password'));
            $customer->setRoles($data['roles']);

            $manager->persist($customer);
        }

        // PRODUITS
        $productsData = [
            ['name' => 'samsung galaxy note 3','price' => 190, 'description' => 'nouveau smartphone'],
            ['name' => 'samsung galaxy note 4', 'price' => 450, 'description' => 'nouveau smartphone'],
            ['name' => 'iphone 6', 'price' => 290, 'description' =>'nouveau smartphone'],
            ['name' => 'huawei mate 10', 'price' => 270, 'description' => 'nouveau smartphone'],
            ['name' => 'samsung galaxy note 6', 'price' => 430, 'description' => 'nouveau smartphone'],
            ['name' => 'iphone 7', 'price' => 500, 'description' => 'nouveau smartphone'],
            ['name' => 'iphone 9', 'price' => 340, 'description' => 'nouveau smartphone'],
            ['name' => 'iphone 10', 'price' => 920, 'description' => 'nouveau smartphone'],
            ['name' => 'iphone 10 pro', 'price' => 270, 'description' => 'nouveau smartphone'],
            ['name' => 'oppo', 'price' => 170, 'description' => 'nouveau smartphone'],
            ['name' => 'huawei p smart', 'price' => 370, 'description' => 'nouveau smartphone'],
            ['name' => 'huawei mate 12', 'price' => 540, 'description' => 'nouveau smartphone']
        ];

        foreach ($productsData as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);

            $manager->persist($product);
        }


        $manager->flush();
    }
}
