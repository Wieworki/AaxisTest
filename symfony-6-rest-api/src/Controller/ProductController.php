<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Product;

#[Route('/api', name: 'product_')]
class ProductController extends AbstractController
{
    #[Route('/product/create', name: 'product_create', methods: ['post'])]
    public function create(EntityManagerInterface $doctrine, ValidatorInterface $validator, Request $request): JsonResponse
    {
        $response = [];

        $data = json_decode($request->getContent(),true);
        if ($data) {
            $product = new Product();
            $product->setSku($data['sku']);
            $product->setProductName($data['product_name']);
            $product->setDescription($data['description']);

            $errors = $validator->validate($product);
            if (count($errors) > 0) {
                $response = [
                    'message' => 'The data sent was invalid'
                ];
                $response["error description"] = [];
                foreach ($errors as $error) {
                    $response["error description"][] = "Invalid value: " . $error->getInvalidValue() . ". " . $error->getMessage();
                }
            } else {
                $doctrine->persist($product);
                $doctrine->flush();
                $response = [
                    'message' => 'The product was created successfully'
                ];
            }
        } else {
            $response = [
                'message' => 'No data received'
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

    #[Route('/product/update', name: 'product_update', methods:['post'])]
    public function update(EntityManagerInterface $doctrine, ValidatorInterface $validator, Request $request): JsonResponse
    {
        $response = [];
        $data = json_decode($request->getContent(),true);
        if ($data) {
            foreach ($data as $row) {
                $sku = $row['sku'];
                $product = $doctrine->getRepository(Product::class)->findOneBy(array('sku' => $sku));
                if ($product == null) {
                    $response[] = "Error updating product with sku " . $sku;
                } else {
                    $product->setProductName($row['product_name']);
                    $product->setDescription($row['description']);

                    $errors = $validator->validate($product);
                    if (count($errors) > 0) {
                        $response[] = "Error updating product with sku " . $sku;
                    } else {
                        $doctrine->persist($product);
                    }
                }
            }
            $doctrine->flush();
            if (!$response) {
                // No errors
                $response[] = "All the products where updated correctly";
           }
        }
        return $this->json($response);
    }
}
