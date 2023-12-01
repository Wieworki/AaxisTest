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
        if (is_array($data) && !empty($data)) {
            $errors = $this->processProductCreate($data, $validator, $doctrine);
            if (!$errors) {
                $response[] = "All the products where created correctly";
            } else {
                $response = $errors;
            }
        } else {
            $response = [
                'message' => 'JSON structure recieved is invalid'
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
        if (is_array($data) && !empty($data)) {
            $errors = $this->processProductUpdate($data, $validator, $doctrine);
            if (!$errors) {
                $response[] = "All the products where updated correctly";
            } else {
                $response = $errors;
            }
        } else {
            $response[] = "JSON structure recieved is invalid";
        }
        return $this->json($response);
    }


    /**
     * @param array $data
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $doctrine
     * @return array
     */
    private function processProductCreate(array $data, ValidatorInterface $validator, EntityManagerInterface $doctrine) {
        $msges = [];
        $counter = 0;
        foreach ($data as $row) {
            $counter++;
            if ($this->isProductJSONStructureValid($row)) {
                $product = new Product();
                $product->setSku($row['sku']);
                $product->setProductName($row['product_name']);
                $product->setDescription($row['description']);
                $errors = $this->isCreateProductJSONDataValid($product, $validator, $doctrine); 
                if ($errors) {
                    $msges[] = $errors;
                } else {
                    $doctrine->persist($product);
                }
            } else {
                $msges[] = "Product number ".$counter." has an invalid JSON structure and could not be procesed.";
            }
        }
        $doctrine->flush();
        return $msges;
    }

    /**
     * @param string $data 
     * @return bool
     */
    private function isProductJSONStructureValid($data) {
        if (is_array($data) && array_key_exists('sku', $data) && array_key_exists('product_name', $data) && array_key_exists('description', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Product $product
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $doctrine
     * @return array
     *     
     */
    private function isCreateProductJSONDataValid(Product $product, ValidatorInterface $validator, EntityManagerInterface $doctrine) {
        $response = [];
        $productExists = $doctrine->getRepository(Product::class)->findOneBy(array('sku' => $product->getSku()));
        if ($productExists) {
            $response[] = "There already exists a product with sku: " . $product->getSku();
        } else {
            $errors = $validator->validate($product);
            if (count($errors) > 0) {
                $response = [
                    'message' => 'The data sent was invalid'
                ];
                $response["error description"] = [];
                foreach ($errors as $error) {
                    $response["error description"][] = "Invalid value: " . $error->getInvalidValue() . ". " . $error->getMessage();
                }
            }
        }
        return $response;
    }

    /**
     * @param array $data
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $doctrine
     * @return array
     */
    private function processProductUpdate(array $data, ValidatorInterface $validator, EntityManagerInterface $doctrine) {
        $msges = [];
        $counter = 0;
        foreach ($data as $row) {
            $counter++;
            if ($this->isProductJSONStructureValid($row)) {
                $sku = $row['sku'];
                $product = $doctrine->getRepository(Product::class)->findOneBy(array('sku' => $sku));
                if ($product == null) {
                    $msges[] = "Error updating product with sku " . $sku;
                } else {
                    $product->setProductName($row['product_name']);
                    $product->setDescription($row['description']);

                    $errors = $validator->validate($product);
                    if (count($errors) > 0) {
                        $msges[] = "Error updating product with sku " . $sku;
                    } else {
                        $doctrine->persist($product);
                    }
                }
            } else {
                $msges[] = "Product number ".$counter." has an invalid JSON structure and could not be procesed.";
            }
        }
        $doctrine->flush();
        return $msges;
    }
}