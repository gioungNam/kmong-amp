<?php
	require_once 'template/header.php';
    require_once 'service/board_service.php';


    $oMemberService = new MemberService();
    $oBoardService = new BoardService();
?>

<div class="container mt-4">
<?php if (isset($_SESSION['user_id'])) : ?>
    <div class="row justify-content-start">
        <div class="col-md-4">      
                <div class="card">
                    <div class="card-body">
                        <p class="card-text"><b><?php echo $_SESSION['nickname'] ?></b>님. 어서오세요!</p>
                        <?php if (isset($_SESSION['profile_picture'])) : ?>
                            <img src="<?= $_SESSION['profile_picture'] ?>" class="card-img-top" alt="Profile Picture">
                        <?php endif; ?>
                    </div>
                </div>
        </div>
        <!-- 아래는 화제게시글 목록 -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">인기 게시글</h5>
                        <?php
                            // getFreeBoardListTop10 함수 호출
                            $aFreeBoardList = $oBoardService->getFreeBoardListTop10();
                        ?>
                        <?php if (!empty($aFreeBoardList)) : ?>
                            <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" style="width: 20%;"></th>
                                </tr>
                            </thead>
                                <tbody>
                                    <?php foreach ($aFreeBoardList as $aBoard) : ?>
                                        <tr>
                                            <td>
                                                <a href="board/post.php?id=<?php echo $aBoard['id']; ?>&type=free">
                                                    <?php echo $aBoard['title']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo "+".$aBoard['likes']; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>게시글이 없습니다.</p>
                        <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- 화제게시글 목록 끝 -->

    </div>
    <div class="row mt-4 mb-4">
        <div class="col-md-4"></div>
        <!-- 좋아요 순 회원 목록 -->
        <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">핫유저 Top 10</h5>
                        <?php
                        $aLikesTopMembers = $oMemberService->getMemberOrderBy('likes', 10);

                        if (isset($aLikesTopMembers['result']) && $aLikesTopMembers['result'] && !empty($aLikesTopMembers['data'])) :
                        ?>
                            <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" style="width: 20%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php foreach ($aLikesTopMembers['data'] as $i => $aMember) : ?>
                                        <tr>
                                            <td>
                                                <?php echo ($i + 1).'.'; ?>
                                            </td>
                                            <td>
                                                <?php echo $aMember['game_nickname']; ?>
                                            </td>
                                            <td>
                                               <?php echo '+'.$aMember['likes'] ?> 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </ul>
                        <?php else : ?>
                            <p>회원 정보가 없습니다.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 레벨 순 회원 목록 -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">랭킹 Top 10</h5>
                        <?php
                        $aLevelTopMembers = $oMemberService->getMemberOrderBy('level', 10);

                        if (isset($aLevelTopMembers) && $aLevelTopMembers['result'] && !empty($aLevelTopMembers['data'])) :
                        ?>
                            <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" style="width: 20%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php foreach ($aLevelTopMembers['data'] as $i => $aMember) : ?>
                                        <tr>
                                            <td>
                                                <?php echo ($i + 1).'.'; ?>
                                            </td>
                                            <td>
                                                <?php echo $aMember['game_nickname']; ?>
                                            </td>
                                            <td>
                                               <?php echo 'lv.'.$aMember['level']; ?> 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>회원 정보가 없습니다.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    </div>
    <?php else : require_once 'auth/login_form.php' ?>
    <?php endif; ?>   
</div>

</body>
</html>

