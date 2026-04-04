<?php
// controllers/NotificationController.php

require_once 'models/Notification.php';

class NotificationController
{
    /**
     * Entry method that routes the AJAX requests
     */
    public function handleRequest()
    {
        // Enforce session security - this should only be called by logged in users
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = getConnection();
        $notifModel = new Notification($db);
        $user_id = $_SESSION['user_id'];

        $action = isset($_GET['action']) ? $_GET['action'] : '';

        header('Content-Type: application/json');

        if ($action === 'fetch') {
            // Action 1: Get the badge count and the top 5 messages
            $count = $notifModel->getUnreadCount($user_id);
            $latest = $notifModel->getLatestUnread($user_id, 5);

            echo json_encode([
                'success' => true,
                'count' => $count,
                'notifications' => $latest
            ]);
            exit;
            
        } elseif ($action === 'read') {
            // Action 2: Mark a specific message as read
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['notification_id'])) {
                $success = $notifModel->markAsRead((int)$input['notification_id'], $user_id);
                
                // Return the new count so the JS badge updates instantly
                $newCount = $notifModel->getUnreadCount($user_id);
                
                echo json_encode([
                    'success' => $success,
                    'count' => $newCount
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Missing ID']);
            }
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            exit;
        }
    }
}
