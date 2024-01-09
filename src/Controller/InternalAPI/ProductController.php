<?php

declare(strict_types=1);

namespace App\Controller\InternalAPI;

use App\Entity\Product;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function array_map;
use function json_decode;

#[Route('products')]
#[Nelmio\Areas(['internal'])]
#[OA\Tag('Products')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    #[Route(
        path: '',
        name: 'internal_create_product',
        methods: ['POST']
    )
    ]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductRequest'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Product created',
        content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
    )]
    public function create(Request $request): Response
    {
        $request->request = new ParameterBag(json_decode($request->getContent(), true));

        $id = $request->request->get('id');
        $name = $request->request->get('name');

        $product = $this->productService->create($id, $name);

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
        ], Response::HTTP_CREATED);
    }

    #[Route(
        path: '',
        name: 'internal_get_all_products',
        methods: ['GET']
    )]
    #[OA\QueryParameter(name: 'limit', schema: new OA\Schema(type: 'number', example: 10))]
    #[OA\QueryParameter(name: 'created_at', schema: new OA\Schema(type: 'string', format: 'date-time', example: '2023-12-18'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Product list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ProductResponse')
        )
    )]
    public function all(): Response
    {
        $products = $this->productService->all();

        return $this->json(array_map(function (Product $product): array {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
            ];
        }, $products));
    }

    #[Route(
        path: '/{id}',
        name: 'internal_get_product_by_id',
        methods: ['GET']
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Product details',
        content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
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
        name: 'internal_update_product',
        methods: ['PUT']
    )]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductRequest'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Product fully updated',
        content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
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
        name: 'internal_patch_product',
        methods: ['PATCH']
    )]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductRequest'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Product partially updated',
        content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')
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
        name: 'internal_delete_product',
        methods: ['DELETE']
    )]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Product deleted',
        content: null
    )]
    public function delete(string $id): Response
    {
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
