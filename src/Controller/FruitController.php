<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Form\FruitType;
use App\Repository\FruitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FruitController extends AbstractController
{
    /**
     * @Route("/", name="fruit_index")
     */
    public function index(Request $request, PaginatorInterface $paginator, FruitRepository $fruitRepository): Response
    {
        $data = $request->query->all();
        $name = $data['name'] ?? null;
        $family = $data['family'] ?? null;

        $queryBuilder = $fruitRepository->createQueryBuilder('f');

        if ($name) {
            $queryBuilder
                ->where('f.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if ($family) {
            $queryBuilder
                ->andWhere('f.family LIKE :family')
                ->setParameter('family', '%'.$family.'%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('fruit/index.html.twig', [
            'fruits' => $pagination,
        ]);
    }

    /**
     * @Route("/fruit/{id}/add-to-favorites", name="fruit_add_to_favorites", methods={"POST"})
     */
    public function addToFavorites(Fruit $fruit, Request $request): Response
    {
        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        if (count($favorites) >= 10) {
            $this->addFlash('error', 'The favorites list is full. You cannot add more than 10 items.');
            return $this->redirectToRoute('fruit_index');
        }

        if (!in_array($fruit->getId(), $favorites)) {
            $favorites[] = $fruit->getId();
            $session->set('favorites', $favorites);
            $this->addFlash('success', 'The fruit has been added to favorites.');
        } else {
            $this->addFlash('warning', 'The fruit is already in favorites.');
        }

        return $this->redirectToRoute('fruit_index');
    }

    /**
     * @Route("/fruit/{id}/remove-from-favorites", name="fruit_remove_from_favorites", methods={"GET"})
     */
    public function removeFromFavorites(Fruit $fruit, Request $request): Response
    {
        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        $index = array_search($fruit->getId(), $favorites);
        if ($index !== false) {
            array_splice($favorites, $index, 1);
            $session->set('favorites', $favorites);
            $this->addFlash('success', 'The fruit has been removed from favorites.');
        } else {
            $this->addFlash('warning', 'The fruit is not in favorites.');
        }

        return $this->redirectToRoute('favorites');
    }

    /**
     * @Route("/favorites", name="favorites")
     */
    public function favorites(Request $request, FruitRepository $fruitRepository): Response
    {
        $session = $request->getSession();
        $favorites = $session->get('favorites', []);

        $fruits = $fruitRepository->findByIdWithNutrition($favorites);

        $totalNutritionFacts = [
            'carbohydrates' => 0,
            'proteins' => 0,
            'fat' => 0,
            'calories' => 0,
            'sugar' => 0
        ];

        foreach ($fruits as $fruit) {
            $totalNutritionFacts['carbohydrates'] += $fruit->getNutrition()->getCarbohydrates();
            $totalNutritionFacts['proteins'] += $fruit->getNutrition()->getProtein();
            $totalNutritionFacts['fat'] += $fruit->getNutrition()->getFat();
            $totalNutritionFacts['calories'] += $fruit->getNutrition()->getCalories();
            $totalNutritionFacts['sugar'] += $fruit->getNutrition()->getSugar();
        }

        // dd($fruits);

        return $this->render('fruit/favorites.html.twig', [
            'fruits' => $fruits,
            'totalNutritionFacts' => $totalNutritionFacts,
        ]);
    }
}
