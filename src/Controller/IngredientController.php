<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * This controller displays all ingredients from BDD
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), // query NOT result
            $request->query->getInt('page', 1), // page number
            12 // limit per page
        );

        return $this->render(
            'pages/ingredient/index.html.twig',
            ['ingredients' => $ingredients]
        );
    }

    /**
     * This controller displays a form to add an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            // saving ingredient to Bdd
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'ingrédient a bien été ajouté !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->renderForm('pages/ingredient/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * This controller displays a form to update an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Ingredient $ingredient
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Ingredient $ingredient): Response
    {        
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            // saving ingredient to Bdd
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'ingrédient a bien été modifié !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->renderForm('pages/ingredient/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
