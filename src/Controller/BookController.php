<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/book')]
final class BookController extends AbstractController
{
    #[Route(name: 'app_book_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        BookRepository $bookRepository,
        PaginatorInterface $paginator
    ): Response {
        $categoryId = $request->query->get('category');
        $qb = $bookRepository->createQueryBuilder('b')
            ->leftJoin('b.category', 'c');

        if ($categoryId) {
            $qb->where('c.id = :id')->setParameter('id', $categoryId);
        }

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            8
        );

        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $pagination,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
        ]);
    }
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/add', name: 'app_book_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();

            if ($coverFile) {
                $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverFile->guessExtension();

                try {
                    $coverFile->move(
                        $this->getParameter('covers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // log ou message flash ici
                }

                $book->setCoverImage($newFilename);
            }

            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/add.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book, BookRepository $bookRepository): Response
    {
        $previousBook = $bookRepository->createQueryBuilder('b')
            ->where('b.id < :id')
            ->setParameter('id', $book->getId())
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $nextBook = $bookRepository->createQueryBuilder('b')
            ->where('b.id > :id')
            ->setParameter('id', $book->getId())
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'previousBook' => $previousBook,
            'nextBook' => $nextBook,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();

            if ($coverFile) {
                $oldFilename = $book->getCoverImage();

                $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverFile->guessExtension();

                try {
                    $coverFile->move(
                        $this->getParameter('covers_directory'),
                        $newFilename
                    );

                  
                    if ($oldFilename) {
                        $oldPath = $this->getParameter('covers_directory') . '/' . $oldFilename;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                } catch (FileException $e) {
                    
                }

                $book->setCoverImage($newFilename);
            }

            $em->flush();
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/add-to-library', name: 'book_add_to_library', methods: ['POST'])]
    public function addToLibrary(Book $book, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && !$user->getBooks()->contains($book)) {
            $user->addBook($book);
            $em->flush();
        }

        $this->addFlash('success', 'Livre ajouté à votre bibliothèque.');

        return $this->redirectToRoute('app_book_index');
    }

    #[Route('/{id}/remove-from-library', name: 'book_remove_from_library', methods: ['POST'])]
    public function removeFromLibrary(Book $book, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && $user->getBooks()->contains($book)) {
            $user->removeBook($book);
            $em->flush();
        }
        
        $this->addFlash('success', 'Livre retiré de votre bibliothèque.');

        return $this->redirectToRoute('app_account');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
        
            $filename = $book->getCoverImage();
            if ($filename) {
                $filePath = $this->getParameter('covers_directory') . '/' . $filename;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute('app_book_index');
    }

}
