<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../service/board_service.php';
require_once '../const/common.php';

// 게시글 ID 확인
if (isset($_GET['boardId']) && !empty($_GET['boardId'])) {
    $boardId = $_GET['boardId'];
} else {
    echo json_encode(['result' => false, 'msg' => '게시글 ID가 유효하지 않습니다.']);
    exit;
}

// 댓글 조회
$oBoardService = new BoardService();
$aCommentsResult = $oBoardService->getCommentsByBoardId($boardId, REPLY);

if ($aCommentsResult['result']) {
    echo json_encode(['result' => true, 'data' => $aCommentsResult['data']]);
} else {
    echo json_encode(['result' => false, 'msg' => $aCommentsResult['msg']]);
}

?>