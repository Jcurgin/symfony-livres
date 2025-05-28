<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Héroïque Fantaisie');
        $manager->persist($category);

        for ($i = 1; $i <= 20; $i++) {
            $book = new Book();
            $book->setTitle("Livre $i");
            $book->setAuthor("Auteur $i");
            $book->setDescription("Ceci est la description du livre $i.");
            $book->setPublicationDate(new \DateTimeImmutable('2023-01-' . str_pad($i, 2, '0', STR_PAD_LEFT)));
            $book->setCategory($category);
            $manager->persist($book);
        }

        $manager->flush();
    }
}