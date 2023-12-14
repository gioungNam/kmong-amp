<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../service/member_service.php";

// AJAX 요청이 아니면 종료
if (!isset($_GET['search'])) {
    echo json_encode(array(
        'result' => false,
        'msg' => '검색할 닉네임을 입력해주세요!'
    ));
    exit();
}

// 사용자가 로그인되어 있는지 확인
if (empty($_SESSION['user_id'])) {
    echo json_encode(array(
        'result' => false,
        'msg' => '로그인이 필요한 서비스입니다.'
    ));
    exit();
}

// 검색어 가져오기
$searchTerm = trim($_GET['search']);

// MemberService 객체 생성
$memberService = new MemberService();

// 검색된 유저 정보 가져오기
$searchResult = $memberService->getMemberInfoByGameName($searchTerm); 

if (!$searchResult['result']) {
    echo json_encode(array(
        'result' => false,
        'msg' => $searchResult['msg']
    ));
    exit();
}

echo json_encode($searchResult);
exit();

?>