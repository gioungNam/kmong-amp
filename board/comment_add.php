<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['boardId']) && isset($_POST['comment'])) {
        $boardId = $_POST['boardId'];
        $comment = $_POST['comment'];

        if (isset($_SESSION['user_id'])) {
            require_once '../service/board_service.php';
            require_once '../const/common.php';

            $oBoardService = new BoardService();
            $result = $oBoardService->addComment($boardId, REPLY, $comment, $_SESSION['user_id']);

            if ($result['result']) {
                echo json_encode(array('result' => true, 'msg' => '댓글이 추가되었습니다.'));
            } else {
                echo json_encode(array('result' => false, 'msg' => $result['msg']));
            }
        } else {
            echo json_encode(array('result' => false, 'msg' => '로그인이 필요합니다.'));
        }
    } else {
        echo json_encode(array('result' => false, 'msg' => '유효하지 않은 요청입니다.'));
    }
}
?>