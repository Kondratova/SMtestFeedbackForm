<?php

namespace App\Acme\Core;

use App\Acme\Core\Model;
use PDO;

final class FeedbackModel extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, full_name, email, message, created_at
             FROM feedback
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function create(string $fullName, string $email, string $message): bool
    {
        $sql = 'INSERT INTO feedback (full_name, email, message)
                VALUES (:full_name, :email, :message)';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':full_name' => $fullName,
            ':email'     => $email,
            ':message'   => $message,
        ]);
    }
}
