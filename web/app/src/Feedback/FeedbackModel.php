<?php

declare(strict_types=1);

namespace App\Acme\Feedback;

use App\Acme\Core\Model;
use PDO;

final class FeedbackModel extends Model
{
    public function getByEmail(string $email): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, full_name, email, message, created_at
         FROM feedback
         WHERE email = :email
         ORDER BY created_at DESC'
        );

        $stmt->execute([':email' => $email]);

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
