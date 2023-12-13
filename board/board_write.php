<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../service/board_service.php';

$oBoardService = new BoardService();

$aResult = $oBoardService->boardWrite($_POST);

echo json_encode($aResult);

?>