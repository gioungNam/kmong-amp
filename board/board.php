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
        <a href="board_form.php?type=<?php echo $boardType; ?>" class="btn btn-primary">글쓰기</a>
    </div>

    <!-- 게시글 목록 표시 -->
    <table class="table">
        <thead>
            <tr>
                <th>번호</th>
                <th scope="col" style="width: 40%;">제목</th>
                <th scope="col" style="width: 15%;">작성자</th>
                <th scope="col">작성일</th>
                <th scope="col">추천수</th>
                <th scope="col">조회수</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paginatedPosts as $post) : ?>
                <tr>
                    <td><?php echo $post['id']; ?></td>
                    <td><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></td>
                    <td><?php echo $post['nickname']; ?></td>
                    <td><?php echo $post['created_at']; ?></td>
                    <td><?php echo $post['likes']; ?></td>
                    <td><?php echo isset($post['views']) ? $post['views'] : 0; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

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