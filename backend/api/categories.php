<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';
include_once '../models/Category.php';

$database = new Database();
$db = $database->connect();
$category = new Category($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $result = $category->index();
        $categories_arr = $result->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $categories_arr
        ]);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (empty($data->name)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Category name is required'
            ]);
            break;
        }
        
        $category_data = [
            'name' => htmlspecialchars(strip_tags($data->name)),
            'color' => $data->color ?? '#667eea'
        ];
        
        if ($category->create($category_data)) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create category'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
}