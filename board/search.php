<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../template/header.php';

if (empty($_SESSION['user_id'])): ?>
        <script>
            alert('로그인이 필요한 서비스입니다.')
            window.history.back();
        </script>  
<?php else:

    ?>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <!-- 검색 바 -->
                    <form id="searchForm" action="" method="get">
                        <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="캐릭터 이름 검색" name="search" id="searchInput" value="">
                            <button class="btn btn-primary" type="button" onclick="searchGameName()">검색</button>
                        </div>
                    </form>
                </div>
            </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">유저 정보</h5>
                                    <div id="userInfo">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 오른쪽 컬럼 - 방명록 -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">방명록</h5>
                                    <!-- 방명록 입력 폼 -->
                                    <form id="guestbookForm" action="" method="post">
                                        <input type="hidden" id="target_id" name="target_id" value="">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="guestbookMessage" name="guestbookMessage" required>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="addGuestbook()">작성</button>
                                    </form>

                                    <div class="mt-3" id="guestbookList">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
      
<?php endif; ?>


<script>
                function searchGameName() {
                    var searchTerm = $('#searchInput').val().trim();

                    if (searchTerm == '') {
                            alert('검색할 닉네임을 입력해주세요!');
                            return;
                        }

                        // AJAX로 search_gamename.php 호출
                        $.ajax({
                            url: 'search_gamename.php',
                            type: 'GET',
                            data: {
                                search: searchTerm
                            },
                            dataType: 'json',
                            success: function(response) {

                                if (response.result) {
                                    // 유저 정보를 userInfo 영역에 출력
                                    var userData = response.data; // 받아온 유저 정보

                                    var userInfoHtml = '<div class="card">';
                                    userInfoHtml += '<img src="' + userData.profile_path + '" class="card-img-top" alt="Profile Image">';
                                    userInfoHtml += '<div class="card-body">';
                                    userInfoHtml += '<h5 class="card-title">' + userData.game_nickname + '</h5>';
                                    userInfoHtml += '<p class="card-text">레벨: ' + userData.level + '</p>';
                                    userInfoHtml += '<p class="card-text">커뮤니티 닉네임: ' + userData.nickname + '</p>';
                                    userInfoHtml += '</div></div>';

                                    $('#userInfo').html(userInfoHtml);


                                    // 검색한 user_id로 업데이트
                                    $('#target_id').val(userData.user_id);
                                    
                                    // 방명록 데이터 출력
                                    var guestbookListHtml = '';
                                    if (userData.guestbook && userData.guestbook.length > 0) {
                                        $.each(userData.guestbook, function(index, guestbook) {
                                            guestbookListHtml += '<div class="guestbook-post">';
                                            guestbookListHtml += '<p class="post-content"><span class="nickname" style="font-weight: bold;">' + guestbook.nickname + '</span> ' + guestbook.comment + '</p>';
                                            guestbookListHtml += '</div>';
                                        });
                                        $('#guestbookList').html(guestbookListHtml);
                                    } else {
                                        $('#guestbookList').html('<p>방명록이 없습니다.</p>');
                                    }

                                } else {
                                    alert(response.msg);
                                }
                                
                            },
                            error: function() {
                                console.log('Error in AJAX request');
                            }
                        });
                    }

                    function addGuestbook() {
                        var toUserId = $('#target_id').val(); // 검색된 유저의 ID를 동적으로 할당해야 합니다.
                        var fromUserId = '<?php echo $_SESSION["user_id"]; ?>'; // 현재 로그인한 유저의 ID
                        var message = $('#guestbookMessage').val().trim();

                        if (message === '') {
                            alert('방명록을 입력하세요!');
                            return;
                        }

                        // AJAX로 add_guestbook.php 호출
                        $.ajax({
                            url: 'add_guestbook.php',
                            type: 'POST',
                            data: {
                                toUserId: toUserId,
                                fromUserId: fromUserId,
                                message: message
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log(response);

                                if (response.result) {
                                    // 추가된 방명록을 동적으로 표시
                                    var newGuestbookHtml = '<p><strong><?php echo $_SESSION['nickname'] ?></strong> ' + response.data.comment + '</p>';
                                    $('#guestbookList').append(newGuestbookHtml);

                                    // 입력 폼 초기화
                                    $('#guestbookMessage').val('');
                                } else {
                                    alert(response.msg);
                                }
                            },
                            error: function() {
                                console.log('Error in AJAX request');
                            }
                        });
                    }

            </script>