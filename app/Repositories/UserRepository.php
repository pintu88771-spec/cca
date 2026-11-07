<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Throwable;

final class UserRepository
{
    public function find(int $id): ?array
    {
        return $this->one('SELECT * FROM users WHERE id = :value LIMIT 1', $id);
    }

    public function findByMobile(string $mobile): ?array
    {
        return $this->one('SELECT * FROM users WHERE mobile = :value LIMIT 1', $mobile);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->one('SELECT * FROM users WHERE email = :value LIMIT 1', $email);
    }

    /**
     * Create a self-registered donor. Public signup is always bhaktpremi.
     *
     * @param array{name: string, mobile: string, email: ?string, password: string, locality: ?string} $data
     */
    public function createDonor(array $data): int
    {
        $pdo = $this->pdo();

        $stmt = $pdo->prepare(
            "INSERT INTO users (role, name, mobile, email, password_hash, locality, show_public, status)
             VALUES ('bhaktpremi', :name, :mobile, :email, :password_hash, :locality, 1, 'active')"
        );
        $stmt->execute([
            'name'          => $data['name'],
            'mobile'        => $data['mobile'],
            'email'         => $data['email'] ?: null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'locality'      => $data['locality'] ?: null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /** @param array{name: string, email: ?string, locality: ?string, message: ?string, show_public: bool, is_anonymous_default: bool} $data */
    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->pdo()->prepare(
            'UPDATE users
             SET name = :name,
                 email = :email,
                 locality = :locality,
                 message = :message,
                 show_public = :show_public,
                 is_anonymous_default = :anonymous
             WHERE id = :id'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'email'       => $data['email'] ?: null,
            'locality'    => $data['locality'] ?: null,
            'message'     => $data['message'] ?: null,
            'show_public' => $data['show_public'] ? 1 : 0,
            'anonymous'   => $data['is_anonymous_default'] ? 1 : 0,
            'id'          => $id,
        ]);
    }

    public function updatePassword(int $id, string $password): void
    {
        $stmt = $this->pdo()->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->execute([
            'hash' => password_hash($password, PASSWORD_DEFAULT),
            'id'   => $id,
        ]);
    }

    public function deleteAccountPhoto(int $id): void
    {
        $stmt = $this->pdo()->prepare('UPDATE users SET photo = NULL WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function updatePhoto(int $id, string $path): void
    {
        $stmt = $this->pdo()->prepare('UPDATE users SET photo = :path WHERE id = :id');
        $stmt->execute(['path' => $path, 'id' => $id]);
    }

    private function one(string $sql, string|int $value): ?array
    {
        try {
            $stmt = $this->pdo()->prepare($sql);
            $stmt->execute(['value' => $value]);
            $row = $stmt->fetch();

            return $row ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    private function pdo(): PDO
    {
        $pdo = Database::connection();

        if (!$pdo instanceof PDO) {
            throw new \RuntimeException('Database connection is unavailable.');
        }

        return $pdo;
    }
}
