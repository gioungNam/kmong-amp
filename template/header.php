<?php 
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Document</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9"
            crossorigin="anonymous"
        />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
        <header class="p-2 bg-black">
            <div class="container d-flex justify-content-between align-items-center p-0">
                <p class="p-1 h2 text-white fw-bold p-0 m-0">LoCurma</p>
                <p class="text-white p-0 m-0">대충 모코코 이미지 들어갈 예정</p>
                <!-- php문 삽입 해서 로그인 하면 등록, 로그아웃으로 변경하셈 -->
                <p class="p-0 m-0">
                    <a href="/member/login.php"><button type="button" class="btn btn-primary pt-2">로그인</button></a>
                    <a href="/member/signup.php"><button type="button" class="btn btn-light text-primary pt-2">회원가입</button></a>
                </p>
            </div>
        </header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-3 py-1">
            <div class="container justify-content-center">
                <ul class="navbar-nav justify-content-between" style="width: 100%">
                    <li class="nav-item text-center">
                        <a class="nav-link fw-bold" href="notice.php">공지사항</a>
                    </li>
                    <li class="nav-item text-center">
                        <a class="nav-link fw-bold" href="freeboard.php">자유게시판</a>
                    </li>
                    <li class="nav-item text-center">
                        <a class="nav-link fw-bold" href="imageboard.php">커마게시판</a>
                    </li>
                    <li class="nav-item text-center">
                        <a class="nav-link fw-bold" href="inquiry.php">문의</a>
                    </li>
                    <li class="nav-item text-center">
                        <a class="nav-link fw-bold" href="search.php">검색/의견등록</a>
                    </li>
                </ul>
            </div>
        </nav>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
            crossorigin="anonymous"
        ></script>
    </body>
</html>