<?php
 if (session_status() === PHP_SESSION_NONE) {   
    session_start();
 }

require_once "../model/board_model.php";
require_once "../const/common.php";

class BoardService {

    /**
     * 게시글 모델 객체
     */
    private $oBoardModel = null;

    private $oObject = null;

    public function __construct() {
        if ($this->oBoardModel == null) {
            $this->oBoardModel = new BoardModel();
        }
    }

    /**
     * 게시글 작성
     */
    public function boardWrite($aFormData) {
        $aResult = array(
            'result' => false,
            'msg' => '' 
        );

        // 유효성 검사
        if (empty($aFormData) || is_array($aFormData) === false) {
            $aResult['msg'] = "유효하지 않은 요청입니다.";
            return $aResult;
        }

        // type 체크
        $aValidTypeList = BoardConst::getBoardType(); 
        if (empty($aFormData['boardType']) || in_array($aFormData['boardType'], $aValidTypeList) === false)  {
            $aResult['msg'] = "유효하지 않은 요청입니다.";
            return $aResult;
        }

         // 로그인 여부 체크
         if (empty($_SESSION['user_id'])) {
            $aResult['msg'] = "로그인이 필요합니다.";
            return $aResult;
        }

        // 필수값 체크
        $aRequiredList = array(
            'title' => '제목',
            'content' => '내용'
        );

        // form 데이터 담을 array 변수
        $aData = array(
            'member_grade' => 'basic'
        );

        foreach ($aRequiredList as $sKey => $sName) {
            if (empty($aFormData[$sKey]))  {
                $aResult['msg'] = "'".$sName."' 항목이 비어 있습니다!";
                return $aResult;
            } 

            // insert data 세팅
            $aData[$sKey] = $aFormData[$sKey];
        }

        // 게시글 type
        $aData['board_type'] = $aFormData['boardType'];

       


        // 게시글 생성
        return $this->oBoardModel->write($aData);
        
    }


    /**
     * 들어온 게시판 type에 대한 게시글 리스트 반환
     */
    public function getBoardListByType($sType) {
        $aBoardList = array();

        // type 체크
        $aValidTypeList = BoardConst::getBoardType(); 
        // 유효하지 않은 타입은 빈 배열 반환
        if (empty($sType) || in_array($sType, $aValidTypeList) === false)  {
            return $aBoardList;
        }


        // 데이터 가져오기
        $aBoardList = $this->oBoardModel->getBoardList($sType);

        // 유효하지 않은 데이터면 빈 배열 반환
        if (is_array($aBoardList) === false || count($aBoardList) <= 0) {
            return array();
        }

        return $aBoardList;
    }
}


