<?php
	require_once '../template/header.php';
?>


<?php if (empty($_SESSION['user_id'])) : ?>
<script src="signup.js"></script>

<div class="container d-flex justify-content-center align-items-center">
    <div class="card" style="max-width: 400px;">
        <div class="card-body">
            <h1 class="card-title text-center">회원가입</h1>
            <form id="signupForm" method="post" action="signup.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="user_id" class="form-label">아이디</label>
                    <input type="text" id="user_id" name="user_id" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="nickname" class="form-label">닉네임</label>
                    <input type="text" id="nickname" name="nickname" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">비밀번호</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">비밀번호 확인</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="character_name" class="form-label">인게임 캐릭터 명</label>
                    <input type="text" id="character_name" name="character_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="level" class="form-label">레벨</label>
                    <input type="number" id="level" name="level" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="profile_picture" class="form-label">프로필 사진</label>
                    <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                </div>

                <div class="text-center">
                <button type="button" class="btn btn-primary" onclick="submitForm()">회원가입</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php else : ?>
    <div class="row justify-content-center">
        <label class="form-label">이미 로그인 상태입니다.</label>
    </div>
<?php endif; ?>

</body>
</html>