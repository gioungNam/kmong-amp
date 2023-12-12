<?php
 if (session_status() === PHP_SESSION_NONE) {   
    session_start();
 }

require_once "../model/board_model.php";
require_once "../model/member_model.php";
require_once "../const/common.php";

class BoardService {

    /**
     * 게시글 모델 객체
     */
    private $oBoardModel = null;

    /**
     * 회원 모델 객체
     */
    private $oMemberModel = null;


    public function __construct() {
        if ($this->oBoardModel == null) {
            $this->oBoardModel = new BoardModel();
        }

        if ($this->oMemberModel == null) {
            $this->oMemberModel = new MemberModel();
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


    /**
     * 게시글 단건 조회하기
     */
    public function getBoardById($boardId)
    {
        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );

        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글 입니다.';
            return $aResult;
        }

        // 게시글 가져오기
        $aBoard = $this->oBoardModel->getBoardById($boardId);

        // 게시글이 존재하는 경우
        if ($aBoard['result'] === true) {
            $aResult['result'] = true;
            $aResult['data'] = $aBoard['data'];
        } else {
            $aResult['msg'] = "해당 게시글을 찾을 수 없습니다.";
        }

        return $aResult;
    }


    /**
     * 조회수 증가
     */
    public function increaseViewCount($boardId)
    {
        if (empty($boardId) || !is_numeric($boardId)) {
            return false;
        }

        // 조회수 증가
        return $this->oBoardModel->increaseViewCount($boardId);
    }

    /**
     * 댓글 추가
     */
    public function addComment($boardId, $type, $value, $mappingId) {

        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );

        // board_id 체크
        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글 입니다.';
            return $aResult;
        }

        // type 체크
        $aValidType = BoardConst::getBoardValueType();
        if (!in_array($type, $aValidType)) {
            $aResult['msg'] = '유효하지 않은 타입 입니다.';
            return $aResult;
        }

        return $this->oBoardModel->addComment($boardId, $type, $value, $mappingId);
    }


    /**
     * 게시글의 댓글 조회
     */
    public function getCommentsByBoardId($boardId, $type) {
        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );

        // board_id 체크
        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글 입니다.';
            return $aResult;
        }

        // type 체크
        $aValidType = BoardConst::getBoardValueType();
        if (!in_array($type, $aValidType)) {
            $aResult['msg'] = '유효하지 않은 타입 입니다.';
            return $aResult;
        }

        return $this->oBoardModel->getCommentsByBoardId($boardId, $type); 

    }


    /**
     * 좋아요 처리
     */
    public function likePost($boardId) {
        $aResult = array(
            'result' => false,
            'msg' => ''
        );

        // 게시글 ID 유효성 검사
        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글입니다.';
            return $aResult;
        }

        // 좋아요 기록 체크
        $aMemberInfoRes = $this->oMemberModel->getMemberValueByIdAndType($_SESSION['user_id'], LIKES);

        if ($aMemberInfoRes['result'] === true) {

            if (empty($aMemberInfoRes['data']) === false) {
                $aInfo = $aMemberInfoRes['data'];

                if (in_array($boardId, $aInfo)) {
                    $aResult['msg'] = '이미 해당 게시글에 좋아요를 눌렀습니다!';
                    return $aResult;
                }
            }
            
        }

        // 좋아요 처리
        $aLikeResult = $this->oBoardModel->incrementLikes($boardId);

        if ($aLikeResult['result'] === false) {
            $aResult['msg'] = $aLikeResult['msg'];

            return $aResult;
        } 


        // 유저 eav 테이블에 좋아요 기록 insert
        $aInsertResult =  $this->oMemberModel->insertMemberValue($_SESSION['user_id'], LIKES, $boardId);

        if ($aInsertResult['result'] === false) {
            $aResult['msg'] = $aInsertResult['msg'];

            return $aResult;
        } 

        return array(
            'result' => true,
            'data' => $aLikeResult['data']
        );
    }
}


