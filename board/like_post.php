<?php
session_start();

require_once "../service/board_service.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 게시글 ID 확인
    $boardId = isset($_POST['boardId']) ? $_POST['boardId'] : null;

    // 로그인 여부 확인
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['result' => false, 'msg' => '로그인이 필요합니다.']);
        exit;
    }

    // 좋아요 기능 처리
    $oBoardService = new BoardService();
    $result = $oBoardService->likePost($boardId); 

    echo json_encode($result);
} else {
    // POST 요청이 아닌 경우 에러 응답
    echo json_encode(['result' => false, 'msg' => '잘못된 요청입니다.']);
}
?>