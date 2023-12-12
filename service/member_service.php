<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../model/member_model.php";

    class MemberService {
            
        /**
         * 회원 모델 객체
         */
        private $oMemberModel = null;


        public function __construct() {
            if ($this->oMemberModel == null) {
                $this->oMemberModel = new MemberModel();
            }
        }


        /**
         * 회원가입 폼 데이터 check
         */
        public function checkFormData($aSignUpForm) {
            $aResult = array(
                'result' => false,
                'msg' => '' 
            );

            // form 데이터 빈값 check
            if (empty($_POST) || !is_array($aSignUpForm)) {
                $aResult['msg'] = "유효하지 않은 요청입니다.";
                return $aResult;
            }

            // 패스워드 체크
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $aResult['msg'] = "패스워드 입력이 패스워드 확인과 다릅니다.";
                return $aResult;
            }

            // 필수값 체크
            $aRequiredList = array(
                'user_id' => '아이디',
                'nickname' => '닉네임',
                'password' => '비밀번호',
                'confirm_password' => '비밀번호 확인',
                'character_name' => '인게임 캐릭터 명',
                'level' => '레벨'
            );

            // form 데이터 담을 array 변수
            $aData = array(
                'member_grade' => 'basic'
            );

            foreach ($aRequiredList as $sKey => $sName) {
                if (empty($_POST[$sKey]))  {
                    $aResult['msg'] = "'".$sName."' 항목이 비어 있습니다!";
                    return $aResult;
                } 

                // 빈 값이 아니면 데이터 세팅 (비밀번호 확인은 제외)
                if ($sKey !== 'confirm_password')
                    $aData[$sKey] = $_POST[$sKey];
            }

            // 존재하는 회원인지 체크
            $aMemberInfo = $this->oMemberModel->selectMemberByMemberId($aData['user_id']);

            if (empty($aMemberInfo) === false  && is_array($aMemberInfo)) {
                $aResult['msg'] = "이미 존재하는 회원 id 입니다.";
                return $aResult;
            }

            // 회원 id가 admin일 경우 admin 회원으로 변경
            if ($aData['user_id'] == 'admin') {
                $aData['member_grade'] = 'admin';
            }

            // 유효성 체크 통과
            return array(
                'result' => true,
                'data' => $aData
            );
        
        }


        /**
         *  회원가입
         */
        public function signUp($aSignUpForm) {
            // 빈 값 체크
            if (is_array($aSignUpForm) === false || empty($aSignUpForm)) {
                return array(
                    'result' => false,
                    'msg' => '회원 가입 요청값이 빈 값입니다.'
                );
            }

            // 회원 가입
            return $this->oMemberModel->insertMember($aSignUpForm);
        }


        /**
         * 프로필 사진 업로드
         */
        public function uploadProfilePicture($aProfilePicture) {

            // 파일 없으면 그냥 리턴
            if (is_array($aProfilePicture) === false || empty($aProfilePicture)) {
                return;
                    
            }

            // 파일 업로드를 위한 설정 (예: 업로드 폴더 경로)
            $sUploadPath = "../uploads/";

            // 업로드할 파일의 원본 파일명
            $sOriginalFileName = $aProfilePicture['name'];

            // 업로드된 파일이 저장될 경로 및 파일명
            $sUploadFilePath = $sUploadPath . basename($sOriginalFileName);

            // 파일 업로드 시 에러 체크
            if ($aProfilePicture['error'] !== 0) {
                return array(
                    'result' => true,
                    'msg'=>"파일 업로드 중 에러가 발생했습니다."
                );
            }

            // 업로드된 파일을 지정한 경로로 이동
            if (move_uploaded_file($aProfilePicture['tmp_name'], $sUploadFilePath)) {
                // 성공적으로 업로드된 경우
                // 여기에서 데이터베이스에 파일 경로 등을 저장하는 로직을 추가할 수 있습니다.
                return array(
                    'result' => true, 
                    'path' => $sUploadFilePath);
            } else {
                return array(
                    'result' => false,
                    'msg' => "파일을 업로드하는 도중 문제가 발생했습니다."
                );
            }
        }


        /**
         * 로그인
         */
        public function login($sUserId, $sPassword) {
            $aResult = array(
                'result' => false,
                'msg' => ''
            );
    
            // 입력값이 빈 값인지 확인
            if (empty($sUserId) || empty($sPassword)) {
                $aResult['msg'] = '아이디 또는 비밀번호를 입력하세요.';
                return $aResult;
            }
    
            // 사용자 정보 가져오기
            $aMemberInfo = $this->oMemberModel->selectMemberByMemberId($sUserId);
    
            // 사용자 정보가 없거나 비밀번호가 일치하지 않으면 로그인 실패
            if (empty($aMemberInfo) || $aMemberInfo['password'] !== $sPassword) {
                $aResult['msg'] = '아이디 또는 비밀번호가 올바르지 않습니다.';
                return $aResult;
            }
    
            // 로그인 성공 시 사용자 정보 반환
            return array(
                'result' => true,
                'user_id' => $aMemberInfo['user_id'],
                'nickname' => $aMemberInfo['nickname'],
                'profile_path' => $aMemberInfo['profile_path']
            );
        }


        /**
         * 게시글 좋아요 여부 체크
         */
        public function isLikedBoard($iBoardId) {
            $bReturn = false;

            // 필수 파라미터 체크
            if (empty($_SESSION['user_id']) || empty($iBoardId)) {  
                return $bReturn;
            }


            // 좋아요 체크
            // 좋아요 기록 체크
            $aMemberInfoRes = $this->oMemberModel->getMemberValueByIdAndType($_SESSION['user_id'], LIKES);

            if ($aMemberInfoRes['result'] === true) {

                if (empty($aMemberInfoRes['data']) === false) {
                    $aInfo = $aMemberInfoRes['data'];

                    if (in_array($iBoardId, $aInfo)) {
                        $bReturn = true;
                    }
                }
                
            }

            return $bReturn;
        }
    }