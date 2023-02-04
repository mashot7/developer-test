<?php

class Products {

  protected String $api_url;

  protected array $products;

  /** @var \Database */
  protected Database $db;

  protected PDO $pdo;

  public function __construct($db, $api_url) {
    //
    $this->api_url = $api_url;
    $this->db = $db;
    $this->pdo = $this->db->getPdo();
    $this->products = $this->fetchProducts();
  }

  public function fetchProducts() {

    $endpoint = $this->api_url;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $data = curl_exec($ch);

    curl_close($ch);

    return json_decode($data, TRUE);
  }

  public function insertAndUpdate() {

    $data = $this->products;

    $query = "INSERT INTO categories (remote_id, name, name_fi)
        VALUES (:remote_id, :name, :name_fi)";
    $insertCategoryStmt = $this->pdo->prepare($query);

    $query = "INSERT INTO products (remote_id, name, name_fi, description)
        VALUES (:remote_id, :name, :name_fi, :description)";
    $insertProductStmt = $this->pdo->prepare($query);

    $query = "INSERT INTO variations (product_id, data, price, price_usd, price_eur)
        VALUES (:product_id, :data, :price, :price_usd, :price_eur)";
    $insertVariationStmt = $this->pdo->prepare($query);

    $query = "INSERT INTO product_categories (product_id, category_id)
        VALUES (:product_id, :category_id)";
    $insertProductCategoriesStmt = $this->pdo->prepare($query);

    // Loop through the products
    foreach ($data["products"] as $product) {
      // Prepare the product data for insertion
      $product_remote_id = $product["id"] ?? NULL;
      $product_name = $product["name"];
      $product_name_fi = $product["name_fi"] ?? NULL;
      $product_description = $product["description"];

      // Check if the product already exists in the database
      $product_exists = FALSE;
      if ($product_remote_id) {
        $query = "SELECT * FROM products
          WHERE remote_id = '$product_remote_id'";
        $selectProduct = $this->pdo->prepare($query);
        $selectProduct->execute();
        $selectProduct->setFetchMode(PDO::FETCH_ASSOC);
        $product_exists = $selectProduct->fetch();
      }

      // Insert or update the product data
      if ($product_exists) {
        // Check what's changed and update
        $productId = $product_exists['id'];
        $updatedColumns = [];
        foreach ($product as $key => $value) {
          if ($key == 'id') {
            continue;
          }
          if (isset($product_exists[$key])) {
            if ($product_exists[$key] != $value) {
              $updatedColumns[] = $key;
            }
          }
        }
        if (!empty($updatedColumns)) {
          $query = "";
          foreach ($updatedColumns as $key) {
            $query .= "
              SET $key='$product[$key]'";
          }
          $query = "UPDATE products $query
            WHERE id='$productId'";
          $updateProduct = $this->pdo->prepare($query);
          $updateProduct->execute();
        }

      }
      else {
        $insertProductStmt->execute([
          'remote_id' => $product_remote_id,
          'name' => $product_name,
          'name_fi' => $product_name_fi,
          'description' => $product_description,
        ]);
        $productId = $this->pdo->lastInsertId();
      }

      $actualCategories = [];
      // Loop through the categories
      foreach ($product["categories"] as $category) {
        // Prepare the category data for insertion
        $categoryRemoteId = $category["id"] ?? NULL;
        $categoryName = $category["name"];
        $categoryNameFi = $category["name_fi"] ?? NULL;

        // Check if the category already exists in the database
        $category_exists = FALSE;
        if ($categoryRemoteId) {
          $query = "SELECT * FROM categories
            WHERE remote_id = '$categoryRemoteId'";
          $selectCategory = $this->pdo->prepare($query);
          $selectCategory->execute();
          $selectCategory->setFetchMode(PDO::FETCH_ASSOC);
          $category_exists = $selectCategory->fetch();
//          dpm('Category Exists: ' . ($category_exists ? 'true' : 'false'));
        }

        // Insert or update the category data
        if ($category_exists) {
          // Check what's changed and update
          $categoryId = $category_exists['id'];
          $updatedColumns = [];
          foreach ($category as $key => $value) {
            if ($key == 'id') {
              continue;
            }
            if (isset($category_exists[$key])) {
              if ($category_exists[$key] != $value) {
                $updatedColumns[] = $key;
              }
            }
          }
          if (!empty($updatedColumns)) {
            $query = "";
            foreach ($updatedColumns as $key) {
              $query .= "
              SET $key='$category[$key]'";
            }
            $query = "UPDATE categories $query
            WHERE id='$categoryId'";
            $updateCategory = $this->pdo->prepare($query);
            $updateCategory->execute();
          }
        }
        else {
          $insertCategoryStmt->execute([
            'remote_id' => $categoryRemoteId,
            'name' => $categoryName,
            'name_fi' => $categoryNameFi,
          ]);
          $categoryId = $this->pdo->lastInsertId();
          $insertProductCategoriesStmt->execute([
            'product_id' => $productId,
            'category_id' => $categoryId,
          ]);
        }
        $actualCategories[] = $categoryId;
      }

      if ($actualCategories) {
        $actualCategories = implode(',', $actualCategories);
        $query = "SELECT id, category_id FROM product_categories
            WHERE product_id = '$productId'
            AND category_id NOT IN ($actualCategories)";
        $selectProductCategoriesStmt = $this->pdo->prepare($query);
        $selectProductCategoriesStmt->execute();
        $selectProductCategoriesStmt->setFetchMode(PDO::FETCH_ASSOC);

        // Remove idle categories
        while ($fetch = $selectProductCategoriesStmt->fetch()) {
          $query = "DELETE FROM product_categories
            WHERE id = '{$fetch['id']}'";
          $this->pdo->prepare($query)->execute();
          $query = "DELETE FROM categories
            WHERE id = '{$fetch['category_id']}'";
          $this->pdo->prepare($query)->execute();
        }
      }

      foreach ($product["variations"] as $variation) {
        $variation_price = $variation['price'];
        unset($variation['price']);
        $variation_price_usd = $variation['price_usd'] ?? NULL;
        if (isset($variation['price_usd'])) {
          unset($variation['price_usd']);
        }
        $variation_price_eur = $variation['price_eur'] ?? NULL;
        if (isset($variation['price_eur'])) {
          unset($variation['price_eur']);
        }
        $data = !empty($variation) ? json_encode($variation) : NULL;
        $query = sprintf(
          "SELECT * FROM variations
         WHERE product_id = '$productId'
         AND price = '$variation_price'
         AND price_usd %s
         AND price_eur %s
         AND %s",
          $variation_price_usd ? "= '$variation_price_usd'" : "IS NULL",
          $variation_price_eur ? "= '$variation_price_eur'" : "IS NULL",
          $data ? "JSON_CONTAINS(data, '$data');" : "data IS NULL",
        );
        $selectVariation = $this->pdo->prepare($query);
        $selectVariation->execute();
        $selectVariation->setFetchMode(PDO::FETCH_ASSOC);
        $variation_exists = $selectVariation->fetch();

        if ($variation_exists) {
          // Check what's changed and update
        }
        else {
          $insertVariationStmt->execute([
            'product_id' => $productId,
            'data' => $data,
            'price' => $variation_price,
            'price_usd' => $variation_price_usd ?? NULL,
            'price_eur' => $variation_price_eur ?? NULL,
          ]);
        }
      }
    }
  }
}