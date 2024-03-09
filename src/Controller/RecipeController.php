<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', requirements: ["id" => "\d+", "slug" => "[a-z0-9-]+"])]
    public function index(): Response
    {
        return new Response("Recette");
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ["id" => "\d+", "slug" => "[a-z0-9-]+"])]
    public function show(string $slug, int $id): Response
    {
        return new Response("Recette : $slug");
    }
}
