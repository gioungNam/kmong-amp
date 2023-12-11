<?php
session_start(); // 세션을 사용하기 위해 세션을 시작합니다.

// 세션 변수 해제 (로그아웃)
unset($_SESSION['user_id']);
unset($_SESSION['nickname']);
unset($_SESSION['profile_picture']);

// 세션 종료
session_destroy();

// 로그아웃 후 메인 페이지로 리다이렉션 또는 다른 동작 수행 가능
header("Location: /");
exit();
?>