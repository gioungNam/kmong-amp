function submitForm() {
    // 폼 데이터를 FormData로 가져오기
    var formData = new FormData(document.getElementById('signupForm'));

    // Ajax를 사용하여 PHP 파일 호출
    $.ajax({
        type: 'POST',
        url: 'signup.php',
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