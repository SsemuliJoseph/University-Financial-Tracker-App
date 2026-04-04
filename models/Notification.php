<?php
// models/Notification.php
// Manages real-time notifications for users

class Notification
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Gets the latest unread notifications for a user (Top 5 for the dropdown)
     */
    public function getLatestUnread($user_id, $limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT notification_id, message, type, created_at 
            FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets the total count of unread notifications for the badge
     */
    public function getUnreadCount($user_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['count'] : 0;
    }

    /**
     * Marks a specific notification as read so it stops showing up
     */
    public function markAsRead($notification_id, $user_id)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $notification_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Inserts a new notification for a user
     */
    public function createNotification($user_id, $message, $type = 'info')
    {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $message, $type);
        return $stmt->execute();
    }
}
