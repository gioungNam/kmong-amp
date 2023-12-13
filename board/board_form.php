<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../template/header.php';
require_once '../const/common.php';
require_once '../service/board_service.php';

// 상수로 정의된 게시판 타입 배열
$validBoardTypes = [
    'notice' => 'notice',
    'free' => 'free',
    'img' => 'img',
    'inquiry' => 'inquiry',
    'search' => 'search',
];

// 게시판 타입 체크
$boardType = isset($_GET['type']) && array_key_exists($_GET['type'], $validBoardTypes) ? $_GET['type'] : '';

// 잘못된 접근 처리
if (empty($boardType)) {
    echo '<script>alert("잘못된 접근입니다."); window.location.href = "/";</script>';
    exit;
}

// 게시글 ID 및 type 체크
$boardId = isset($_GET['id']) ? $_GET['id'] : null;
$updateType = isset($_GET['action']) ? $_GET['action'] : '';




// 수정 모드인 경우, 게시글 정보 가져오기
$postInfo = array();
if ($updateType === 'update' && !empty($boardId)) {
    $oBoardService = new BoardService();
    $postResult = $oBoardService->getBoardById($boardId);

    if ($postResult['result']) {
        $postInfo = $postResult['data'];
    } else {
        echo '<script>alert("게시글을 찾을 수 없습니다."); window.location.href = "/";</script>';
        exit;
    }
}

?>

<div class="container mt-4">
    <h2 class="mb-3"><?php echo BoardConst::convertTypeToName($boardType); ?></h2>

    <form id="boardForm" action="board_write.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="boardType" value="<?php echo $boardType; ?>">
        <input type="hidden" name="boardId" value="<?php echo $boardId; ?>">
        <input type="hidden" name="action" value="<?php echo $updateType; ?>">
        <div class="mb-3">
            <label for="title" class="form-label">제목</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($postInfo['title']) ? $postInfo['title'] : ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">내용</label>
            <textarea class="form-control" id="content" name="content" rows="5" required><?php echo isset($postInfo['content']) ? trim($postInfo['content']) : ''; ?></textarea>
        </div>

        <?php if ($boardType === 'img') : ?>
            <div class="mb-3">
                <label for="image" class="form-label">이미지</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
        <?php endif; ?>

        <button id="submitBtn" type="button" class="btn btn-primary">작성</button>
    </form>
</div>

<script>
    function submitBoardForm() {
        var formData = new FormData(document.getElementById('boardForm'));

        <?php if ($boardType === 'img') : ?>
            // 파일이 첨부되었는지 확인
            var fileInput = document.getElementById('image');
            if (fileInput && fileInput.files.length === 0) {
                alert('이미지를 첨부해주세요.');
                return;
            }

            formData.append('image', fileInput.files[0]);
        <?php endif; ?>

    

    $.ajax({
        type: "POST",
        url: "board_write.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            let res = JSON.parse(response);
            // 실패시
            if (res.result === false) {
                alert(res.msg);
            } else {
                // 성공시, 수정 모드인 경우와 아닌 경우에 따라 리다이렉트
                if ("<?php echo $updateType; ?>" === "update") {
                    window.location.href = "post.php?id=" + res.data.board_id;
                } else {
                    window.location.href = "board.php?type=" + res.data.board_type;
                }
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX request failed: " + error);
        }
    });
}

// 이벤트 핸들러 등록
$(document).ready(function () {
    $("#submitBtn").on("click", submitBoardForm);
});
</script>