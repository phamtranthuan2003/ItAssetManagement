<?php
/**
 * File: app/controllers/AuthController.php
 * Controller xử lý đăng nhập, đăng xuất và tài khoản
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function login(): void {
        if (Auth::check()) {
            Auth::user();
            redirect(Auth::landingPath());
        }
        $this->render('auth/login', [
            'pageTitle' => 'Đăng nhập hệ thống'
        ], null); // null layout: trang đăng nhập có layout riêng biệt
    }

    /**
     * Xử lý đăng nhập POST
     */
    public function handleLogin(): void {
        $this->validateCsrf();

        $username = $this->post('username', '');
        $password = $this->post('password', '');

        if (empty($username) || empty($password)) {
            redirect('login', 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!', 'danger');
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            redirect('login', 'Tên đăng nhập hoặc mật khẩu không chính xác!', 'danger');
        }

        if ((int)$user['status'] !== 1) {
            redirect('login', 'Tài khoản của bạn đang bị khóa, vui lòng liên hệ Quản trị viên!', 'danger');
        }

        // Đăng nhập thành công
        Auth::login($user);
        redirect(Auth::landingPath(), 'Chào mừng ' . $user['fullname'] . ' đã đăng nhập thành công!', 'success');
    }

    /**
     * Đăng xuất khỏi hệ thống
     */
    public function logout(): void {
        Auth::logout();
        redirect('login', 'Bạn đã đăng xuất an toàn khỏi hệ thống!', 'info');
    }

    /**
     * Trang xem thông tin cá nhân và đổi mật khẩu
     */
    public function profile(): void {
        Auth::requireLogin();
        $user = $this->userModel->findById(Auth::id());

        $this->render('auth/profile', [
            'pageTitle' => 'Thông tin tài khoản',
            'user'      => $user
        ]);
    }

    /**
     * Đổi mật khẩu cá nhân
     */
    public function updatePassword(): void {
        Auth::requireLogin();
        $this->validateCsrf();

        $currentPass = $this->post('current_password', '');
        $newPass = $this->post('new_password', '');
        $confirmPass = $this->post('confirm_password', '');

        $user = $this->userModel->findByUsername(Auth::user()['username']);

        if (!$user || !password_verify($currentPass, $user['password'])) {
            redirect('profile', 'Mật khẩu hiện tại không đúng!', 'danger');
        }

        if (strlen($newPass) < 6) {
            redirect('profile', 'Mật khẩu mới phải có tối thiểu 6 ký tự!', 'danger');
        }

        if ($newPass !== $confirmPass) {
            redirect('profile', 'Mật khẩu nhập lại không khớp!', 'danger');
        }

        $this->userModel->update(Auth::id(), [
            'fullname' => $user['fullname'],
            'email'    => $user['email'],
            'role'     => $user['role'],
            'status'   => $user['status'],
            'password' => $newPass
        ]);

        redirect('profile', 'Đổi mật khẩu thành công!', 'success');
    }
}
