<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id'])) : ?>
    <script>
        alert('로그인이 필요한 서비스입니다.')
        window.history.back();
    </script>  
<?php else : require_once  '../template/header.php'; ?>
      
<?php endif; ?>


<?php 
require_once '../const/common.php';
require_once '../service/board_service.php';

// 게시판 타입 체크
$aValidBoardTypes = BoardConst::getBoardType();

// 게시판 타입 세팅
$boardType = isset($_GET['type']) && in_array($_GET['type'], $aValidBoardTypes) ? $_GET['type'] : '';

// 잘못된 접근 처리
if (empty($boardType)) {
    echo '<script>alert("잘못된 접근입니다."); window.location.href = "/";</script>';
    exit;
}

// 페이징 처리
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // 페이지당 표시할 게시물 수



// 게시글 리스트 가져오기
$oBoardService = new BoardService();
$posts = $oBoardService->getBoardListByType($boardType);


// 페이징을 위한 배열 슬라이싱
$start = ($page - 1) * $perPage;
$end = $start + $perPage;
$paginatedPosts = array_slice($posts, $start, $perPage);


?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h2 class="mb-3"><?php echo BoardConst::convertTypeToName($boardType); ?></h2>
        <?php
        // $boardType이 'notice'일 경우 어드민 유저인 경우에만 '글쓰기' 버튼을 노출
        if ($boardType === 'notice' && $_SESSION['user_id'] === 'admin' || $boardType != 'notice') : ?>
            <a href="board_form.php?type=<?php echo $boardType; ?>" class="btn btn-primary">글쓰기</a>            
        <?php endif; ?>
    </div>

    <!-- 게시글 목록 표시 -->
    <?php if ($boardType === 'img') : ?>
        <div class="row">
        <?php foreach ($paginatedPosts as $idx => $post) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?php echo (isset($post['image_path'])) ? $post['image_path'] : ''; ?>" class="card-img-top" alt="게시글 이미지">
                    <div class="card-body">
                        <h5 class="card-title"><a href="post.php?id=<?php echo $post['id']; ?>&type=<?php echo $boardType; ?>"><?php echo $post['title']; ?></a></h5>
                        <p class="card-text">작성자: <?php echo $post['nickname']; ?></p>
                        <p class="card-text">추천수: <?php echo $post['likes']; ?></p>
                        <p class="card-text">조회수: <?php echo isset($post['views']) ? $post['views'] : 0; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th scope="col" style="width: 40%;">제목</th>
                    <th scope="col" style="width: 15%;">작성자</th>
                    <th scope="col">작성일</th>
                    <?php if ($boardType === 'inquiry') : ?>
                    <th scope="col">문의상태</th>
                    <?php else : ?>
                        <th scope="col">추천수</th>
                    <?php endif; ?>
                    <th scope="col">조회수</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginatedPosts as $idx => $post) : ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><a href="post.php?id=<?php echo $post['id']; ?>&type=<?php echo $boardType; ?>"><?php echo $post['title']; ?></a></td>
                        <td><?php echo $post['nickname']; ?></td>
                        <td><?php echo $post['created_at']; ?></td>
                        <?php if ($boardType === 'inquiry') : ?>
                            <td><?php echo $post['inquiry_state']; ?></td>
                        <?php else : ?>
                            <td><?php echo $post['likes']; ?></td>
                        <?php endif; ?>
                        <td><?php echo isset($post['views']) ? $post['views'] : 0; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- 페이징 -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for ($i = 1; $i <= ceil(count($posts) / $perPage); $i++) : ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?type=<?php echo $boardType; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>