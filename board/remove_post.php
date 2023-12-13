<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../const/common.php';
require_once '../service/board_service.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 게시글 ID 확인
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $postId = $_POST['id'];

        // 게시글 삭제
        $oBoardService = new BoardService();
        $deleteResult = $oBoardService->deleteBoard($postId); 

        // JSON 형태로 응답
        echo json_encode($deleteResult);
        exit;
    } else {
        // 게시글 ID가 전달되지 않은 경우
        echo json_encode(array('result' => false, 'msg' => '유효하지 않은 게시글입니다.'));
        exit;
    }
} else {
    // POST 요청이 아닌 경우
    echo json_encode(array('result' => false, 'msg' => '잘못된 요청입니다.'));
    exit;
}
?>