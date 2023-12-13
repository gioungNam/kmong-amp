<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../service/member_service.php";

$aResult = array(
    'result' => false,
    'msg' => ''
);

// 필수 파라미터 체크
if (empty($_POST['toUserId']) || empty($_POST['fromUserId']) || empty($_POST['message'])) {
    $aResult['msg'] = '필수 파라미터가 누락되었습니다.';
    echo json_encode($aResult);
    exit;
}

$oMemberService = new MemberService();
$result = $oMemberService->insertGuestbook($_POST['toUserId'], $_POST['message']);

echo json_encode($result);
?>