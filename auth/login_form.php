<?php if (empty($_SESSION['user_id'])) : ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form id="loginForm" method="post" action="login.php">
                    <div class="mb-3">
                        <label for="userid" class="form-label">아이디:</label>
                        <input type="text" id="userid" name="userid" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">비밀번호:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="button" onclick="login()" class="btn btn-primary">로그인</button>
            </form>
        </div>
    </div>

    <script>
        function login() {
            // AJAX를 사용하여 로그인 처리
            var formData = new FormData(document.getElementById('loginForm'));

            $.ajax({
            type: 'POST',
            url: 'auth/login.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // 실패 처리
                let res = JSON.parse(response);
                console.log(res);
                if (res.result === false) {
                    alert(res.msg)
                } else {
                    // 성공시, 메인 페이지 이동
                    window.location.href = "/";
                }
                
            },
            error: function(error) {
                console.error('Ajax 호출 실패:', error);
            }
            });
        }
    </script>
<?php else : ?>
    <div class="row justify-content-center">
        <label class="form-label">이미 로그인 상태입니다.</label>
    </div>
<?php endif; ?>
