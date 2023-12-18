<?php

declare(strict_types=1);

namespace App\Controller\PublicAPI;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function json_decode;
use function sha1;
use function uniqid;

#[Route('products')]
class ProductController extends AbstractController
{
    #[Route(
        path: '',
        name: 'public_create_product',
        methods: ['POST']
    )
    ]
    public function create(Request $request): Response
    {
        $request->request = new ParameterBag(json_decode($request->getContent(), true));
        $name = $request->request->get('name');

        return $this->json([
            'id' => sha1(uniqid('product')),
            'name' => $name,
        ]);
    }

    #[Route(
        path: '',
        name: 'public_get_all_products',
        methods: ['GET']
    )]
    public function all(): Response
    {
        return $this->json([]);
    }

    #[Route(
        path: '/{id}',
        name: 'public_get_product_by_id',
        methods: ['GET']
    )]
    public function get(string $id): Response
    {
        return $this->json([
            'id' => $id,
            'name' => 'Product B',
        ]);
    }

    #[Route(
        path: '/{id}',
        name: 'public_update_product',
        methods: ['PUT']
    )]
    public function put(string $id, Request $request): Response
    {
        $request->request = new ParameterBag(json_decode($request->getContent(), true));
        $name = $request->request->get('name');

        return $this->json([
            'id' => $id,
            'name' => $name,
        ]);
    }

    #[Route(
        path: '/{id}',
        name: 'public_patch_product',
        methods: ['PATCH']
    )]
    public function patch(string $id, Request $request): Response
    {
        $request->request = new ParameterBag(json_decode($request->getContent(), true));
        $name = $request->request->get('name');

        return $this->json([
            'id' => $id,
            'name' => $name,
        ]);
    }

    #[Route(
        path: '/{id}',
        name: 'public_delete_product',
        methods: ['PATCH']
    )]
    public function delete(string $id): Response
    {
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
