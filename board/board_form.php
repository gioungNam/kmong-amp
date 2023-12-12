<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../template/header.php';
require_once '../const/common.php';

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
?>

<div class="container mt-4">
    <h2 class="mb-3"><?php echo BoardConst::convertTypeToName($boardType); ?></h2>

    <form id="boardForm" action="board_write.php" method="post">
        <input type="hidden" name="boardType" value="<?php echo $boardType; ?>">
        <div class="mb-3">
            <label for="title" class="form-label">제목</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">내용</label>
            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
        </div>

        <button id="submitBtn" type="button" class="btn btn-primary">작성</button>
    </form>
</div>

<script>
    function submitBoardForm() {
    var formData = new FormData(document.getElementById('boardForm'));

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
                // 성공시, 메인 페이지 이동
                window.location.href = "board.php?type=" + res.data.board_type;
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