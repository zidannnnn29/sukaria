<?php
/**
 * Controller Autentikasi (login / logout).
 */
final class AuthController
{
    public static function showLogin(): never
    {
        if (Auth::user()) {
            redirect(Auth::role() === 'admin' ? 'admin/dashboard' : 'seller/dashboard');
        }
        render('public', 'auth/login', [
            'pageTitle' => 'Login — ' . SUKARIA_APP_NAME,
            'errors'    => $_SESSION['login_errors'] ?? [],
            'emailOld'  => $_SESSION['login_old_email'] ?? '',
        ]);
    }

    public static function login(): never
    {
        require_post();
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email tidak valid.';
        }
        if ($password === '') {
            $errors['password'] = 'Password wajib diisi.';
        }

        if ($errors) {
            $_SESSION['login_errors']     = $errors;
            $_SESSION['login_old_email']  = $email;
            redirect('login');
        }

        $user = Auth::attempt($email, $password);
        if (!$user) {
            // Bedakan pesan bila akun nonaktif
            $found = User::findByEmail($email);
            if ($found && (int) $found['is_active'] !== 1) {
                flash_set('Akun Anda dinonaktifkan. Hubungi administrator.', 'error');
            } else {
                flash_set('Email atau password salah.', 'error');
            }
            $_SESSION['login_errors']    = [];
            $_SESSION['login_old_email'] = $email;
            redirect('login');
        }

        unset($_SESSION['login_errors'], $_SESSION['login_old_email']);
        flash_set('Selamat datang, ' . $user['name'] . '!');
        redirect($user['role'] === 'admin' ? 'admin/dashboard' : 'seller/dashboard');
    }

    public static function logout(): never
    {
        Auth::logout();
        redirect('login');
    }
}
