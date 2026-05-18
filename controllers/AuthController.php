<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void {
        $error = $_GET['error'] ?? null;
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            header('Location: index.php?page=login&error=fill_all');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            header('Location: index.php?page=login&error=invalid');
            exit;
        }

        if ($user['role'] !== 'seller') {
            header('Location: index.php?page=login&error=not_seller');
            exit;
        }

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['name']     = $user['name'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['verified'] = $user['seller_verified'];

        header('Location: index.php?page=dashboard');
        exit;
    }

    public function showRegister(): void {
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=register');
            exit;
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $bio      = trim($_POST['bio'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $motivation = trim($_POST['motivation'] ?? '');

        $errors = [];
        if (!$name)                         $errors[] = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
        if (strlen($password) < 6)          $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm)         $errors[] = 'Passwords do not match.';
        if (!$motivation)                   $errors[] = 'Motivation statement required.';
        if ($this->userModel->emailExists($email)) $errors[] = 'Email already registered.';

        // ID document upload
        $docPath = null;
        if (!empty($_FILES['id_document']['name'])) {
            $allowed = ['jpg','jpeg','png','pdf'];
            $ext     = strtolower(pathinfo($_FILES['id_document']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors[] = 'ID document must be JPG, PNG, or PDF.';
            } else {
                $filename  = uniqid('doc_') . '.' . $ext;
                $uploadDir = __DIR__ . '/../public/uploads/documents/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $dest = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['id_document']['tmp_name'], $dest)) {
                    $docPath = 'public/uploads/documents/' . $filename;
                } else {
                    $errors[] = 'Failed to upload document.';
                }
            }
        } else {
            $errors[] = 'ID document is required.';
        }

        if ($errors) {
            $_SESSION['reg_errors'] = $errors;
            header('Location: index.php?page=register');
            exit;
        }

        $userId = $this->userModel->createUser([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'bio'           => $bio,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $this->userModel->createVerificationRequest([
            'user_id'          => $userId,
            'motivation'       => $motivation,
            'id_document_path' => $docPath,
        ]);

        header('Location: index.php?page=login&success=registered');
        exit;
    }

    public function logout(): void {
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}