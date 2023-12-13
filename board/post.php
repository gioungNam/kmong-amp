
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../template/header.php';
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

// 댓글 작성 가능 여부 (문의 post인 경우)
$bComment = true;

// 로그인 안되어 있는 경우 댓글 작성 불가
if (!isset($_SESSION['user_id'])) {
    $bComment = false;
}

// 문의 post인 경우, 관리자가 아닌경우 댓글 작성 불가
if (isset($_GET['type']) && $_GET['type'] === 'inquiry' && $_SESSION['user_id'] != 'admin') {
    $bComment = false;
}

?>

<style>
    .centered-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .comment-divider {
        border-top: 1px solid #dee2e6;
    }


    #commentList {
    border-left: none;
    border-right: none;
    border-radius: 0;
    }

    .list-group-item {
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
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
                                    <button class="btn btn-outline-primary" onclick="editPost(<?php echo $postInfo['id']; ?>)">수정</button>
                                    <button class="btn btn-outline-danger" onclick="deletePost(<?php echo $postInfo['id']; ?>)">삭제</button>
                            </div>
                            <?php endif; ?>                  

                    </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- 여기에서 content 출력 -->
                                <p><?php echo nl2br($postInfo['content']); ?></p>
                                <!-- 이미지 출력 부분 추가 -->
                                <?php if (isset($postInfo['image_path'])) : ?>
                                    <div class="text-center" style="margin-bottom: 10px;">
                                        <img src="<?php echo $postInfo['image_path']; ?>" class="img-fluid" alt="게시글 이미지">
                                    </div>
                                <?php endif; ?>
                                <div class="text-center">
                                <?php if (isset($_GET['type']) && $_GET['type'] !== 'inquiry') : ?>
                                    <button type="button" class="btn <?php echo $bPostLiked ? 'btn-primary' : 'btn-outline-primary'; ?>" onclick="likePost(<?php echo $postInfo['id']; ?>)">
                                        <i class="bi bi-hand-thumbs-up"></i> 좋아요 <small id="likeCount">(<?php echo $postInfo['likes']; ?>)</small>
                                    </button>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- 댓글 목록 -->
                <div class="mt-4">
                    <div class="card-body comment-divider">
                        <h5 class="card-title"><?php echo (isset($_GET['type']) && $_GET['type'] === 'inquiry') ? '답변' : '댓글' ?></h5>
                        <ul class="list-group" id="commentList">
                            <!-- 댓글 목록은 AJAX로 동적으로 가져올 예정 -->
                        </ul>
                    </div>
                </div>

                <!-- 댓글 입력창 -->
                <?php if ($bComment === true) : ?>
                    <div class="mt-4">
                        <div class="card-body comment-divider">
                            <h5 class="card-title"><?php echo (isset($_GET['type']) && $_GET['type'] === 'inquiry') ? '답변 작성' : '댓글 작성' ?></h5>
                            <form id="commentForm">
                                <div class="mb-3">
                                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                </div>
                                <button type="button" class="btn btn-primary" onClick="submitComment()">추가</button>
                            </form>
                        </div>
                    </div>
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
        var comment = $('#comment').val().trim();;

        // 빈값체크
        if (comment == '') {
            alert('코멘트 내용을 입력해야 합니다.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'comment_add.php',
            data: { boardId: boardId, comment: comment },
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    // 댓글 입력창 초기화
                    $('#comment').val('');
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

        // 첫 번째 row (유저 닉네임과 작성일자)
        var userInfoRow = $('<div class="row"></div>');
        var userNicknameCol = $('<div class="col-md-6 fw-bold small"></div>').text(comments[i].nickname);
        var commentDateCol = $('<div class="col-md-6 text-right text-muted small"></div>').text(comments[i].comment_created_at);
        userInfoRow.append(userNicknameCol, commentDateCol);

        // 두 번째 row (댓글 내용)
        var commentContentRow = $('<div class="row"></div>');
        var commentContentCol = $('<div class="col-md-12 small"></div>').text(comments[i].value);
        commentContentRow.append(commentContentCol);

        // 삭제 버튼 (세션과 사용자 ID 비교 후 노출 여부 결정)
        if ((comments[i].mapping_id === '<?php echo $_SESSION['user_id']; ?>')) {
            var deleteButton = $('<div class="col-md-12"><button class="btn btn-danger btn-sm float-right" onclick="deleteComment(' + comments[i].id + ')">삭제</button></div>');
            commentContentRow.append(deleteButton);
        } 
        

        // 두 row를 하나의 li에 추가
        commentItem.append(userInfoRow, commentContentRow);

        commentList.append(commentItem);
    }
    }

    // 댓글 삭제
    function deleteComment(commentId) {
        $.ajax({
            type: 'POST',
            url: 'remove_comment.php',
            data: { commentId: commentId },
            dataType: 'json',
            success: function (response) {
                if (response.result) {
                    // 성공 시 댓글 목록 업데이트
                    loadComments(<?php echo $postInfo['id']; ?>);
                } else {
                    alert(response.msg);
                }
            },
            error: function () {
                alert('댓글 삭제 중 오류가 발생했습니다.');
            }
        });
    }

    // 초기 로딩 시 댓글 목록 가져오기
    $(document).ready(function() {
        loadComments(<?php echo $postInfo['id']; ?>);
    });



    function editPost(postId) {
        // postId를 이용해 수정할 게시글의 식별 정보 전달
        window.location.href = 'board_form.php?id=' + postId + '&action=update&type=<?php echo $postInfo['board_type']; ?>';
    }

    function deletePost(postId) {
        // 게시글 삭제 로직
        // postId를 이용해 삭제할 게시글의 식별 정보 전달
        if (confirm("정말로 삭제하시겠습니까?")) {
            // 확인 버튼 클릭 시 삭제 처리
            $.ajax({
                type: 'POST',
                url: 'remove_post.php',
                data: { id: postId },
                dataType: 'json',
                success: function(response) {
                    if (response.result) {
                        // 성공 시 알림 후 페이지 목록으로 이동 처리
                        alert(response.msg);
                        window.location.href = "board.php?type=<?php echo $postInfo['board_type']; ?>";
                    } else {
                        // 실패시
                        alert(response.msg);
                    }
                },
                error: function() {
                    alert('게시글 삭제 중 오류가 발생했습니다.');
                }
            });
        }
    }
</script>


