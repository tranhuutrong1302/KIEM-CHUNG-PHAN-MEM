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

/**
 * Hàm tính tiền cọc theo yêu cầu của Nhóm trưởng
 * Quy tắc: Tiền cọc luôn làm tròn lên hàng nghìn đồng.
 */
function Tinh_Tien_Coc($gia_khoi_diem, $ti_le_coc) {
    if ($gia_khoi_diem < 0) {
        return "Giá trị không hợp lệ";
    }
    
    $tien_coc = $gia_khoi_diem * ($ti_le_coc / 100);
    
    // Làm tròn lên hàng nghìn (ví dụ: 10.550đ -> 11.000đ)
    return ceil($tien_coc / 1000) * 1000;
}
