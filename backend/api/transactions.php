<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';
include_once '../models/Transaction.php';

$database = new Database();
$db = $database->connect();
$transaction = new Transaction($db);

$method = $_SERVER['REQUEST_METHOD'];

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateTransaction($data) {
    $errors = [];
    
    if (empty($data->title)) {
        $errors[] = "Title is required";
    }
    
    if (!isset($data->amount) || $data->amount <= 0) {
        $errors[] = "Valid amount is required";
    }
    
    if (empty($data->currency) || !in_array($data->currency, ['USD', 'EUR', 'GBP', 'MYR'])) {
        $errors[] = "Valid currency is required";
    }
    
    if (empty($data->transaction_date)) {
        $errors[] = "Transaction date is required";
    }
    
    return $errors;
}

switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $result = $transaction->view($_GET['id']);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Transaction not found'
                ]);
            }
        } else {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'currency' => $_GET['currency'] ?? '',
                'category_id' => $_GET['category_id'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'limit' => $_GET['limit'] ?? '',
                'offset' => $_GET['offset'] ?? ''
            ];
            
            $result = $transaction->index($filters);
            $total = $transaction->count($filters);
            $transactions_arr = $result->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $transactions_arr,
                'total' => $total,
                'count' => count($transactions_arr)
            ]);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $errors = validateTransaction($data);
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ]);
            break;
        }
        
        $transaction_data = [
            'title' => sanitizeInput($data->title),
            'amount' => floatval($data->amount),
            'currency' => sanitizeInput($data->currency),
            'category_id' => !empty($data->category_id) ? intval($data->category_id) : null,
            'description' => sanitizeInput($data->description ?? ''),
            'transaction_date' => sanitizeInput($data->transaction_date)
        ];
        
        if ($transaction->create($transaction_data)) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Transaction created successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create transaction'
            ]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if (empty($data->id)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Transaction ID is required'
            ]);
            break;
        }
        
        $errors = validateTransaction($data);
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ]);
            break;
        }
        
        $transaction_data = [
            'title' => sanitizeInput($data->title),
            'amount' => floatval($data->amount),
            'currency' => sanitizeInput($data->currency),
            'category_id' => !empty($data->category_id) ? intval($data->category_id) : null,
            'description' => sanitizeInput($data->description ?? ''),
            'transaction_date' => sanitizeInput($data->transaction_date)
        ];
        
        if ($transaction->update($data->id, $transaction_data)) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update transaction'
            ]);
        }
        break;
        
    case 'DELETE':
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Transaction ID is required'
            ]);
            break;
        }
        
        if ($transaction->delete($_GET['id'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete transaction'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
}