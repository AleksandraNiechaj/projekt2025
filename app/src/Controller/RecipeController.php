<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Form\RecipeForm;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recipe')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'app_recipe_index', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        RecipeRepository $recipeRepository,
        CategoryRepository $categoryRepository
    ): Response {
        // pobieramy przepisy posortowane malejąco po dacie utworzenia
        $qb = $recipeRepository
            ->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC');

        // paginacja: 10 przepisów na stronę
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        // pobieramy też listę wszystkich kategorii (do panelu bocznego/menu)
        $categories = $categoryRepository->findAll();

        return $this->render('recipe/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeForm::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('app_recipe_index');
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', methods: ['GET', 'POST'])]
    public function show(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // formularz dodawania komentarza
        $comment = new Comment();
        $comment->setRecipe($recipe);

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe'      => $recipe,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Recipe $recipe,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(RecipeForm::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_recipe_index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        Recipe $recipe,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->request->get('_token'))) {
            $em->remove($recipe);
            $em->flush();
        }

        return $this->redirectToRoute('app_recipe_index');
    }
}
