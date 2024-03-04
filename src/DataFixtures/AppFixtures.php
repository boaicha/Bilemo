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
