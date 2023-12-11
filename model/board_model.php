<?php
 if (session_status() === PHP_SESSION_NONE) {   
    session_start();
 }

require_once "../const/common.php";

class BoardModel {
    /**
     * 데이터베이스 객체
     */
    private $oDataBase = null;

    public function __construct() {
        if ($this->oDataBase == null) {
            $this->oDataBase = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE, MYSQL_PORT);        
        }
    }


    /**
     * 게시글 insert
     */
    public function write($aInsert) {
        $aResult = array(
            'result' => false,
            'msg' => ''
        );

        // 게시글 insert 쿼리 작성
        $sQuery = "INSERT INTO board (board_type, title, content, user_id) 
            VALUES (
                '" . $this->oDataBase->real_escape_string($aInsert['board_type']) . "',
                '" . $this->oDataBase->real_escape_string($aInsert['title']) . "',
                '" . $this->oDataBase->real_escape_string($aInsert['content']) . "',
                '" . $this->oDataBase->real_escape_string($_SESSION['user_id']) . "'
            )";

        // insert 실행
        if ($this->oDataBase->query($sQuery) === true) {
            // 성공시
            $aResult['result'] = true;
            $aResult['data'] = $aInsert;

        } else {
            $aResult['msg'] = "게시글 등록 중 오류가 발생했습니다.";
        }

        return $aResult;
    }


    /**
     * 해당 타입에 대한 게시글 리스트 반환
     */
    public function getBoardList($sType) {
        $aBoardList = array();


        // 쿼리 작성
        $sQuery = "SELECT * FROM board WHERE board_type = '$sType' ORDER BY id DESC";

        // 쿼리 실행
        $oResult = $this->oDataBase->query($sQuery);

        // 결과값이 있는 경우
        if ($oResult->num_rows > 0) {
            while ($aRow = $oResult->fetch_assoc()) {
                $aBoardList[] = $aRow;
            }
        }

        return $aBoardList;
    }

    public function __destruct() {
        // db연결 종료
        $this->oDataBase->close();
    }

}