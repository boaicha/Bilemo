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
        // $product = new Product();
        // $manager->persist($product);

        // COMMERCIAUX
        $usersData = [
            ['name' => 'simon','first_name' => 'simon', 'username' => 'simoncharbonnier03@gmail.com', 'email' => 'simoncharbonnier03@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'john', 'first_name' => 'john', 'username' => 'johndoe@gmail.com', 'email' => 'johndoe@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'shaun', 'first_name' => 'shaun', 'username' =>'haunwinter@gmail.com','email' => 'shaunwinter@gmail.com', 'roles' => ["ROLE_USER"]],
            ['name' => 'martina', 'first_name' => 'martina', 'username' => 'martinasnow@gmail.com','email' => 'martinasnow@gmail.com', 'roles' => ["ROLE_USER"]],
        ];

        foreach ($usersData as $data) {
            $customer = new Customer();
            $customer->setName($data['name']);
            $customer->setFirstName($data['first_name']);
            $customer->setEmail($data['email']);
            $customer->setPassword($this->hasher->hashPassword($customer, 'password'));
            $customer->setUsername($data['username']);
            $customer->setRoles($data['roles']);

            $manager->persist($customer);
        }

        // PRODUITS
        $productsData = [
            ['name' => 'samsung galaxy note 3','price' => 190, 'description' => 'nouveau smartphone'],
            ['name' => 'samsung galaxy note 4', 'price' => 450, 'description' => 'nouveau smartphone'],
            ['name' => 'iphone 6', 'price' => 290, 'description' =>'nouveau smartphone'],
            ['name' => 'huawei mate 10', 'price' => 270, 'description' => 'nouveau smartphone']
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
