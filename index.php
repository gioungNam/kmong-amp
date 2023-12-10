<?php
	require_once 'template/header.php';
?>
<!-- 로그인 폼 -->
<div class="container mt-4">
    <div class="row justify-content-start">
        <div class="col-md-6">
            <?php if (isset($_SESSION['user_id'])) : ?>
                <div class="card">
                    <div class="card-body">
                        <p class="card-text"><b><?php echo $_SESSION['nickname'] ?></b>님. 어서오세요!</p>
                        <?php if (isset($_SESSION['profile_picture'])) : ?>
                            <img src="<?= $_SESSION['profile_picture'] ?>" class="card-img-top" alt="Profile Picture">
                        <?php endif; ?>
                    </div>
                </div>
            <?php else : ?>
                <form method="post" action="auth/login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">아이디:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">비밀번호:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>

