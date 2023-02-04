<?php
header("Content-Type: application/json");

$data = [
  "products" => [
    [
      "id" => "3568e30a-3cc0-44cc-9bc9-a6b27edad126",
      "name" => "Black DOOM® T-shirt",
      "description" => "Cool T-shirt with DOOM logo.",
      "categories" => [
        [
          "id" => "ced9a008-6dda-412b-8b79-4cd2274a0566",
          "name" => "Gaming apparel",
        ],
      ],
      "variations" => [
        [
          "size" => "S",
          "price" => 25.000,
        ],
        [
          "size" => "M",
          "price" => 25.000,
        ],
        [
          "size" => "L",
          "price" => 25.000,
        ],
      ],
    ],
    [
      "id" => "a2d6471b-ceeb-4c3f-a1ec-70a603fdcc70",
      "name" => "Minecraft coffee mug",
      "description" => "Green coffee mug with Minecraft logo.",
      "categories" => [
        [
          "id" => "3cdff117-c0af-435e-866b-5ddcd9aa4427",
          "name" => "Coffee mugs",
        ],
        [
          "id" => "e57eb5de-a24f-440c-b771-22cbb388cc18",
          "name" => "Minecraft",
        ],
      ],
      "variations" => [
        [
          "price" => 14.900,
        ],
      ],
    ],
    [
      "id" => "8a3a38c0-f3c1-472d-bb06-b5e09f25f45d",
      "name" => "World of Warcraft Outland map wall poster",
      "description" => "A high quality map of the Outlands from World of Warcraft The Burning Crusade expansion. An excellent gift for someone who is a fan of World of Warcraft and The Burning Crusade expansion specifically.",
      "categories" => [
        [
          "id" => "b7cc56ff-97fe-4d08-8076-e54ae2d1b3c2",
          "name" => "Posters",
        ],
      ],
      "variations" => [
        [
          "paper size" => "A1",
          "price" => 19.900,
        ],
        [
          "paper size" => "A2",
          "price" => 16.900,
        ],
      ],
    ],
  ],
  "results" => 3,
];

echo json_encode($data);
