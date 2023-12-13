<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../const/common.php';
require_once '../service/board_service.php';

// 댓글 ID 확인
if (isset($_POST['commentId']) && !empty($_POST['commentId'])) {
    $commentId = $_POST['commentId'];
} else {
    echo json_encode(['result' => false, 'msg' => '댓글 ID가 유효하지 않습니다.']);
    exit;
}

$oBoardService = new BoardService();
$response = $oBoardService->removeComment($commentId); 

echo json_encode($response);


?>