<?php
// 쿠키로부터 로그인 정보 불러오기
$user_pkey = $_COOKIE['user_pkey'] ?? null;
$user_name = $_COOKIE['user_name'] ?? null;

// 쿠키가 없다면 로그인 페이지로 이동
if (!$user_pkey || !$user_name) {
    echo "<script>alert('로그인 후 이용해주세요.'); location.href='signIn.php';</script>";
    exit;
}
?>
