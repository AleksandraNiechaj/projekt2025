<?php
// src/Controller/CommentController.php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-comment' . $comment->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Nieprawidłowy token CSRF.');
        }

        $recipeId = $comment->getRecipe()->getId();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('app_recipe_show', ['id' => $recipeId]);
    }
}
