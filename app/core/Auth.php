<?php
/**
 * Autentikasi berbasis session.
 */
final class Auth
{
    public static function attempt(string $email, string $password): array|false
    {
        $user = User::findByEmail(trim($email));
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        if ((int) $user['is_active'] !== 1) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        self::cacheUser($user);
        return $user;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        if (!isset($_SESSION['user_cache']) || (int) ($_SESSION['user_cache']['id'] ?? 0) !== (int) $_SESSION['user_id']) {
            $user = User::find((int) $_SESSION['user_id']);
            if (!$user || (int) $user['is_active'] !== 1) {
                self::logout();
                return null;
            }
            self::cacheUser($user);
        }
        return $_SESSION['user_cache'];
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u ? (int) $u['id'] : null;
    }

    public static function role(): ?string
    {
        $u = self::user();
        return $u ? $u['role'] : null;
    }

    private static function cacheUser(array $user): void
    {
        $_SESSION['user_cache'] = [
            'id'         => (int) $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'store_name' => $user['store_name'],
        ];
    }
}
