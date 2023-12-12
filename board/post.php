<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../const/common.php';
require_once '../service/board_service.php';
require_once '../service/member_service.php';

// 게시글 ID 확인
if (isset($_GET['id']) && empty(($_GET['id']) === false)) {
    $postId = $_GET['id'];
} else {
    echo '<script>alert("잘못된 접근입니다."); window.location.href = "/";</script>';
    exit;
}

// 게시글 정보 가져오기
$oBoardService = new BoardService();
$aPostResult = $oBoardService->getBoardById($postId);
// 게시글 좋아요 여부
$oMemberService = new MemberService();
$bPostLiked = $oMemberService->isLikedBoard($postId);

// 게시글이 존재하지 않는 경우
if ($aPostResult['result'] === false) {
    echo '<script>alert("'.$aPostResult['msg'].'"); window.location.href = "/";</script>';
    exit;
}

// 데이터 취득
$postInfo = $aPostResult['data'];


// 조회수 증가
$oBoardService->increaseViewCount($postId);

require_once '../template/header.php';
?>

<style>
    .centered-container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .comment-divider {
        border-top: 1px solid #dee2e6;
    }
</style>

<div class="centered-container">
    <div class="container .d-flex justify-content-center">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                    <div class="d-flex align-items-end mt-2">
                        <h2 class="flex-grow-1"><?php echo $postInfo['title']; ?></h2> 
                        <div class="pr-2">
                            <p class="mb-0 p-2"><?php echo $postInfo['created_at']; ?></p>  
                        </div>
                        <div class="pr-2">
                        <p class="mb-0 p-2"><?php echo $postInfo['nickname']; ?></p>
                        </div>
                        <?php
                            // 현재 로그인한 사용자와 게시글 작성자가 동일한 경우 수정, 삭제 버튼 표시 (어드민일 경우도 표시)
                            if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $postInfo['user_id'] || $_SESSION['user_id'] == 'admin')) :
                            ?>
                            <div class="mb-0 p-2">
                                    <button class="btn btn-outline-primary" onclick="editPost()">수정</button>
                                    <button class="btn btn-outline-danger" onclick="deletePost()">삭제</button>
                            </div>
                            <?php endif; ?>                  

                    </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- 여기에서 content 출력 -->
                                <p><?php echo nl2br($postInfo['content']); ?></p>
                                <div class="text-center">
                                <button type="button" class="btn <?php echo $bPostLiked ? 'btn-primary' : 'btn-outline-primary'; ?>" onclick="likePost(<?php echo $postInfo['id']; ?>)">
                                    <i class="bi bi-hand-thumbs-up"></i> 좋아요 <small id="likeCount">(<?php echo $postInfo['likes']; ?>)</small>
                                </button>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- 댓글 목록 -->
                <div class="mt-4">
                    <div class="card-body comment-divider">
                        <h5 class="card-title">댓글</h5>
                        <ul class="list-group" id="commentList">
                            <!-- 댓글 목록은 AJAX로 동적으로 가져올 예정 -->
                        </ul>
                    </div>
                </div>

                <!-- 댓글 입력창 -->
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="mt-4">
                        <div class="card-body comment-divider">
                            <h5 class="card-title">댓글 작성</h5>
                            <form id="commentForm">
                                <div class="mb-3">
                                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                </div>
                                <button type="button" class="btn btn-primary" onClick="submitComment()">추가</button>
                            </form>
                        </div>
                    </div>
                <?php else : ?>
                    <p class="text-muted mt-4">댓글을 작성하려면 로그인이 필요합니다.</p>
                <?php endif; ?>
            </div>
            </div>
    </div>
</div>
<script>

    function likePost(boardId) {
        // Ajax를 통한 좋아요 처리
        $.ajax({
            type: 'POST',
            url: 'like_post.php',
            data: { boardId: boardId },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.result) {
                    // 성공 시 좋아요 수 업데이트
                    $('#likeCount').text('(' + response.data.likes + ')');

                    // 좋아요 상태에 따라 버튼 클래스 업데이트
                    var likeButton = $('#likeCount').parent();
                    likeButton.removeClass('btn-outline-primary');
                    likeButton.addClass('btn-primary');
                } else {
                    alert(response.msg);
                }
            },
            error: function() {
                alert('좋아요 처리 중 오류가 발생했습니다.');
            }
        });
    }

    // 코멘트 작성
    function submitComment() {
        var boardId = <?php echo $postInfo['id']; ?>;
        var comment = $('#comment').val();

        $.ajax({
            type: 'POST',
            url: 'comment_add.php',
            data: { boardId: boardId, comment: comment },
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    // 성공 시 댓글 목록 업데이트
                    loadComments(boardId);
                } else {
                    alert(response.msg);
                }
            },
            error: function() {
                alert('댓글 작성 중 오류가 발생했습니다.');
            }
        });
    }

    // 댓글 불러오기
    function loadComments(boardId) {
        $.ajax({
            type: 'GET',
            url: 'get_comments.php',  // 댓글을 조회하는 API 주소를 넣어주세요
            data: { boardId: boardId },
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    // 댓글 목록을 업데이트하는 함수를 호출하여 화면에 표시
                    updateCommentList(response.data);
                } else {
                    alert(response.msg);
                }
            },
            error: function(error) {
                console.log(error);
                alert('댓글 조회 중 오류가 발생했습니다.');
            }
        });
    }

    // 댓글 목록 업데이트
    function updateCommentList(comments) {
        var commentList = $('#commentList');
        commentList.empty();

        for (var i = 0; i < comments.length; i++) {
            var commentItem = $('<li class="list-group-item"></li>');
            commentItem.text(comments[i].value);
            commentList.append(commentItem);
        }
    }

    // 초기 로딩 시 댓글 목록 가져오기
    $(document).ready(function() {
        loadComments(<?php echo $postInfo['id']; ?>);
    });



    function editPost(postId) {
        // 게시글 수정 로직
        // postId를 이용해 수정할 게시글의 식별 정보 전달
        window.location.href = "edit_post.php?id=" + postId;
    }

    function deletePost(postId) {
        // 게시글 삭제 로직
        // postId를 이용해 삭제할 게시글의 식별 정보 전달
        if (confirm("정말로 삭제하시겠습니까?")) {
            // 확인 버튼 클릭 시 삭제 처리
            // AJAX 등을 사용하여 서버에 삭제 요청을 보낼 수 있음
            alert("삭제되었습니다.");
            window.location.href = "/";
        }
    }
</script>