<?php
function getLoginErrorMessage($username, $password, $userFromDb) {
    if (!$userFromDb) {
        return "Tài khoản không tồn tại!";
    }
    if (!password_verify($password, $userFromDb['password'])) {
        return "Mật khẩu không chính xác!";
    }
    return ""; // Đăng nhập thành công
}
