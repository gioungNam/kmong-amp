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
        $sQuery = "SELECT board.*, member.nickname as nickname 
                    FROM board
                    LEFT JOIN member ON board.user_id = member.user_id 
                    WHERE board_type = '$sType' ORDER BY id DESC";

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

    /**
     * 게시글 단건 조회
     */
    public function getBoardById($boardId)
    {
        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );
    
        // 게시글 조회 쿼리 (board와 member 테이블 조인)
        $sQuery = "SELECT board.*, member.nickname as nickname 
                   FROM board
                   LEFT JOIN member ON board.user_id = member.user_id
                   WHERE board.id = '$boardId'";
    
        // 쿼리 실행
        $oResult = $this->oDataBase->query($sQuery);
    
        // 결과값이 있는 경우
        if ($oResult && $oResult->num_rows > 0) {
            $aResult['result'] = true;
            $aResult['data'] = $oResult->fetch_assoc();
        } else {
            $aResult['msg'] = "게시글을 찾을 수 없습니다.";
        }
    
        return $aResult;
    }


    /**
     * 조회수 증가
     */
    public function increaseViewCount($boardId)
    {
        $sQuery = "UPDATE board SET views = views + 1 WHERE id = '$boardId'";

        return $this->oDataBase->query($sQuery);
    }


    /**
     * likes 컬럼 증가
     */
    public function incrementLikes($boardId) {
        $result = array(
            'result' => false,
            'msg' => ''
        );

        $query = "UPDATE board SET likes = likes + 1 WHERE id = ?";
        $stmt = $this->oDataBase->prepare($query);

        if ($stmt === false) {
            $result['msg'] = "쿼리 준비 중 오류가 발생했습니다.";
            return $result;
        }

        $stmt->bind_param("i", $boardId);
        $stmt->execute();

        if ($stmt->error) {
            $result['msg'] = "데이터 업데이트 중 오류가 발생했습니다.";
        } else {
            $result['result'] = true;

            // 증가된 likes 값을 가져옴
            $query = "SELECT likes FROM board WHERE id = ?";
            $stmt = $this->oDataBase->prepare($query);
            $stmt->bind_param("i", $boardId);
            $stmt->execute();
            $stmt->bind_result($likes);
            $stmt->fetch();

            $result['data'] = array('likes'=> $likes);
        }

        $stmt->close();

        return $result;
    }

    /**
 * 댓글 작성
 */
public function addComment($boardId, $type, $value, $mappingId) {
    $aResult = array(
        'result' => false,
        'msg' => ''
    );

    $sQuery = "INSERT INTO board_value (board_id, mapping_id, type, value) VALUES (?, ?, ?, ?)";
    $stmt = $this->oDataBase->prepare($sQuery);

    if ($stmt === false) {
        $aResult['msg'] = "쿼리 준비 중 오류가 발생했습니다.";
        return $aResult;
    }

    $stmt->bind_param("isss", $boardId, $mappingId, $type, $value);
    $stmt->execute();

    if ($stmt->error) {
        $aResult['msg'] = "댓글 등록 중 오류가 발생했습니다.";
    } else {
        $aResult['result'] = true;
    }

    $stmt->close();

    return $aResult;
}

    /**
     * 댓글 조회
     */
    public function getCommentsByBoardId($boardId, $sType) {
        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );
    
        // 게시글 ID 유효성 검사
        if (empty($boardId) || !is_numeric($boardId)) {
            $aResult['msg'] = '유효하지 않은 게시글입니다.';
            return $aResult;
        }
    
        // 쿼리 작성
        $sQuery = "SELECT * FROM board_value WHERE board_id = '$boardId' AND type = '$sType'";
    
        // 쿼리 실행
        $oResult = $this->oDataBase->query($sQuery);
    
        $aResult['result'] = true;
        // 결과값이 있는 경우
        if ($oResult && $oResult->num_rows > 0) {
            while ($aRow = $oResult->fetch_assoc()) {
                $aResult['data'][] = $aRow;
            }
            
        } else {
            $aResult['data'] = array();
        }
    
        return $aResult;
    }

    public function __destruct() {
        $this->oDataBase->close();
    }

}