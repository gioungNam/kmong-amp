<?php
 if (session_status() === PHP_SESSION_NONE) {   
    session_start();
 }

require_once "../model/board_model.php";
require_once "../model/member_model.php";
require_once "member_service.php";
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

    /**
     * 회원 서비스 객체
     */
    private $oMemberService = null;


    public function __construct() {
        if ($this->oBoardModel == null) {
            $this->oBoardModel = new BoardModel();
        }

        if ($this->oMemberModel == null) {
            $this->oMemberModel = new MemberModel();
        }

        if ($this->oMemberService == null) {
            $this->oMemberService = new MemberService();
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

        // action여부 체크
        $sAction = isset($aFormData['action']) ? $aFormData['action'] : '';

        // 필수값 체크
        $aRequiredList = array(
            'title' => '제목',
            'content' => '내용'
        );

        $aData = array();

        foreach ($aRequiredList as $sKey => $sName) {
            if (empty($aFormData[$sKey]))  {
                $aResult['msg'] = "'".$sName."' 항목이 비어 있습니다!";
                return $aResult;
            } 

            // insert data 세팅
            $aData[$sKey] = $aFormData[$sKey];
        }

        // 회원 등급 (basic : 일반, admin : 운영자)
        $aData['member_grade'] = 'basic';
        // 게시글 type
        $aData['board_type'] = $aFormData['boardType'];

        if ($sAction === 'update') {
            if (empty($aFormData['boardId']) || !is_numeric($aFormData['boardId'])) {
                $aResult['msg'] = '유효하지 않은 게시글 입니다.';
                return $aResult;
            }

            return $this->updateBoard($aFormData['boardId'], $aData);
        }

        // 게시글 생성
        $aBoardResult = $this->oBoardModel->write($aData);

        // 커마게시판인경우, 이미지도 저장 필요
        if ($aFormData['boardType'] == 'img') {
            // 사진 파일 업로드
            if (empty($_FILES['image']['name']) === false) {
                $aUploadResult = $this->oMemberService->uploadProfilePicture($_FILES['image']);
            
                // 실패시 반환
                if ($aUploadResult['result'] === false) {
                    $aResult['msg'] = $aUploadResult['msg'];
                    return $aResult;
                }

                $sPath = isset($aUploadResult['path']) ? $aUploadResult['path'] : '';

                // 사진 파일 경로 insert
                $this->oBoardModel->insertBoardValue($aBoardResult['data']['board_id'], BoardConst::TYPE_IMG, $sPath);
            }
            
        }

        return $aBoardResult;
        
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

        // inquiry 게시판의 경우 inquiry_state 값 가져오기
        if ($sType === BoardConst::BOARD_INQUIRY) {
            foreach ($aBoardList as &$board) {
                $aBoardInfo = $this->oBoardModel->getCommentsByBoardId($board['id'], REPLY);
                $board['inquiry_state'] = (is_array($aBoardInfo['data']) && count($aBoardInfo['data']) > 0) ? BoardConst::INQUIRY_COMPLETE : BoardConst::INQUIRY_WAIT;
            }
        }

        // 커마게시판일 경우, 사진 가져오기
        if ($sType === BoardConst::BOARD_IMAGE) {
            foreach ($aBoardList as &$board) {
                $aBoardInfo = $this->oBoardModel->getBoardValue($board['id'], BoardConst::TYPE_IMG);
                $board['image_path'] = (is_array($aBoardInfo['data']) && count($aBoardInfo['data']) > 0) ? $aBoardInfo['data'][0]['value'] : null;
            }
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
            // 이미지 가져오기
            $aImage = $this->oBoardModel->getBoardValue($aBoard['data']['id'], BoardConst::TYPE_IMG);

            // 이미지가 존재할 경우
            if ($aImage['result'] === true && empty($aImage['data']) === false) {
                $aBoard['data']['image_path'] = $aImage['data'][0]['value'];
            }

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
     * 게시글 삭제
     */
    public function deleteBoard($boardId) {
        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );

        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글 입니다.';
            return $aResult;
        }


        return $this->oBoardModel->deleteBoard($boardId); 
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

        return $this->oBoardModel->insertBoardValue($boardId, $type, $value, $mappingId);
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


    /**
     * 댓글 삭제
     */
    public function removeComment($commentId)
    {
        $aResult = array(
            'result' => false,
            'msg' => ''
        );

        $oBoardModel = new BoardModel();
        $response = $oBoardModel->removeComment($commentId); 

        if ($response['result']) {
            $aResult['result'] = true;
        } else {
            $aResult['msg'] = $response['msg'];
        }

        return $aResult;
    }


    /**
     * 게시글 업데이트
     */
    private function updateBoard($boardId, $aData)
    {
        $aResult = array(
            'result' => false,
            'msg' => '' 
        );

        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글 입니다.';
            return $aResult;
        }

        // 필요한 데이터 확인 및 처리

        $aUpdateData = array(
            'title' => $aData['title'],
            'content' => $aData['content'],
        ); 

        $bResult = $this->oBoardModel->updateBoard($boardId, $aUpdateData);

        if ($bResult) {
            return array('result' => true, 'msg' => '게시글이 수정되었습니다.', 'data' => array('board_id' => $boardId));
        } else {
            return array('result' => false, 'msg' => '게시글 수정에 실패했습니다.');
        }
    }

    /**
     * 코멘트 업데이트
     */
    function updateComment($commentId, $editedContent) {
        $boardModel = new BoardModel();
        return $boardModel->updateComment($commentId, $editedContent);
    }
}


