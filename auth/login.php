<?php
session_start(); 

require_once "../service/member_service.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userid'];
    $password = $_POST['password'];

    $memberService = new MemberService();

    // 사용자 입력을 검증하고 로그인 처리
    $aLoginResult = $memberService->login($userId, $password);

    if ($aLoginResult['result'] === true) {
        // 로그인이 성공하면 세션에 사용자 정보 저장
        $_SESSION["user_id"] = $aLoginResult['user_id'];
        $_SESSION["nickname"] = $aLoginResult['nickname'];
        $_SESSION['profile_picture'] = $aLoginResult['profile_path'];

    } 

    echo json_encode($aLoginResult);
    return;

} else {
    // POST 요청이 아닌 경우에 대한 처리 (예: 리다이렉션 등)
    // 여기서는 간단히 에러 메시지 출력
    echo "잘못된 요청입니다.";
}
?>