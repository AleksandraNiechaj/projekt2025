<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/profile', name: 'admin_profile', methods: ['GET','POST'])]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Dane profilu zostały zapisane.');
            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/password', name: 'admin_password', methods: ['GET','POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$hasher->isPasswordValid($user, $data['oldPassword'])) {
                $form->get('oldPassword')->addError(new FormError('Niepoprawne hasło'));
            } else {
                $user->setPassword(
                    $hasher->hashPassword($user, $data['newPassword'])
                );
                $em->flush();
                $this->addFlash('success', 'Hasło zostało zmienione.');
                return $this->redirectToRoute('admin_password');
            }
        }

        return $this->render('admin/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
