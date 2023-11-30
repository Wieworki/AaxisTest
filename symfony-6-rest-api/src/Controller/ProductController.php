<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Product;

#[Route('/api', name: 'blog_')]
class ProductController extends AbstractController
{
    #[Route('/product/create', name: 'product_create', methods: ['post'])]
    public function create(EntityManagerInterface $doctrine, ValidatorInterface $validator, Request $request): JsonResponse
    {
        $response = [];

        $data = json_decode($request->getContent(),true);
        

        $product = new Product();
        $product->setSku($data['sku']);
        $product->setProductName($data['product_name']);
        $product->setDescription($data['description']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            $response = [
                'message' => 'The data sent was invalid'
            ];
        } else {
            $doctrine->persist($product);
            $doctrine->flush();
            $response = [
                'message' => 'Welcome to your new controller!',
                'path' => 'src/Controller/ProductController.php',
            ];
        }

        return $this->json($response);
    }

    #[Route('/product/list', name: 'product_list', methods:['get'])]
    public function list(EntityManagerInterface $doctrine): JsonResponse
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'product_name' => $product->getProductName(),
                'description' => $product->getDescription(),
                'created_at' => $product->getCreatedAt(),
                'update_at' => $product->getUpdateAt(),
            ];
        }
        return $this->json($data);
    }
}
