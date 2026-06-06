<?php
// Hàm xác định danh mục
function getCategory($row) {
    if (!empty($row['category']) && $row['category'] != 'Khác') return $row['category'];
    $n = mb_strtolower($row['name']);
    if (strpos($n, 'biệt thự') !== false || strpos($n, 'penthouse') !== false || strpos($n, 'đất') !== false) return 'Bất động sản';
    if (strpos($n, 'đồng hồ') !== false || strpos($n, 'patek') !== false || strpos($n, 'richard') !== false || strpos($n, 'rolex') !== false) return 'Đồng hồ';
    if (strpos($n, 'nhẫn') !== false || strpos($n, 'dây') !== false || strpos($n, 'kim cương') !== false || strpos($n, 'đá quý') !== false || strpos($n, 'vòng') !== false) return 'Trang sức';
    if (strpos($n, 'biển') !== false || strpos($n, '30a') !== false) return 'Biển số';
    return 'Xe sang'; 
}
?>
