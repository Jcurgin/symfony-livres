<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/account', name: 'app_account')]
class AccountController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(Security $security, Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $security->getUser();
        $categoryId = $request->query->get('category');

        $books = $user->getBooks();

        if ($categoryId) {
            $books = $books->filter(function ($book) use ($categoryId) {
                return $book->getCategory() && $book->getCategory()->getId() == $categoryId;
            });
        }

        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('account/index.html.twig', [
            'books' => $books,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
        ]);
    }
}
