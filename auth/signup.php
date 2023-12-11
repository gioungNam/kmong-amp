<?php

    require_once "../service/member_service.php";

    
    // member service 객체
    $oMemberService = new MemberService();

    // form 데이터 체크
    $aCheckResult = $oMemberService->checkFormData($_POST);

    // 실패시 json 응답 반환
    if ($aCheckResult['result'] === false) {
        echo json_encode($aCheckResult);
        return;
    }

    // 데이터 존재시
    if (empty($aCheckResult['data']) === false) {

        // profile_picture가 있을 경우에만
        if (empty($_FILES['profile_picture']['name']) === false) {
            
            // 파일 업로드 처리
            $aUploadResult = $oMemberService->uploadProfilePicture($_FILES['profile_picture']);

            if ($aUploadResult['result'] === false) {
                echo json_encode($aUploadResult);
                return;
            }
        }

        // 프로필 사진 data 변수에 추가
        $aCheckResult['data']['profile_path'] = isset($aUploadResult['path']) ? $aUploadResult['path'] : '';
    
        // 회원가입
        $aJoinResult = $oMemberService->signUp($aCheckResult['data']);


        // 회원 가입 결과 json 응답 반환
        echo json_encode($aJoinResult);
    }
   
?>