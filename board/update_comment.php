<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../service/board_service.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['commentId']) || empty($_POST['editedContent'])) {
        echo json_encode(array('result' => false, 'msg' => '올바른 요청이 아닙니다.'));
    }

    $commentId = $_POST['commentId'];
    $editedContent = $_POST['editedContent'];

    $oBoardService = new BoardService();

    $result = $oBoardService->updateComment($commentId, $editedContent); 

    // 결과에 따른 응답
    if ($result) {
        echo json_encode(array('result' => true));
    } else {
        echo json_encode(array('result' => false, 'msg' => '댓글 수정 실패'));
    }
} else {
    echo json_encode(array('result' => false, 'msg' => '올바른 요청이 아닙니다.'));
}


?>