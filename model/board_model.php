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
        VALUES (?, ?, ?, ?)";

        // prepare statement 생성
        $oStmt = $this->oDataBase->prepare($sQuery);

        // 바인딩
        $oStmt->bind_param("ssss", 
            $aInsert['board_type'],
            $aInsert['title'],
            $aInsert['content'],
            $_SESSION['user_id']
        );

        // execute 실행
        if ($oStmt->execute()) {
            // 삽입된 게시글의 ID를 얻기
            $iBoardId = $oStmt->insert_id;

            $aResult['result'] = true;
            $aInsert['board_id'] = $iBoardId;
            $aResult['data'] = $aInsert;

        } else {
            $aResult['msg'] = "게시글 등록 중 오류가 발생했습니다.";
        }

    // statement 종료
    $oStmt->close();

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
    * 코멘트 작성
    */
    public function insertBoardValue($boardId, $type, $value, $mappingId = null) {
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
            $aResult['msg'] = "쿼리 등록 중 오류가 발생했습니다.";
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
        $sQuery = "SELECT bv.*, m.nickname, bv.created_at as comment_created_at 
                FROM board_value bv
                LEFT JOIN member m ON bv.mapping_id = m.user_id
                WHERE bv.board_id = '$boardId' AND bv.type = '$sType'";
    
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

    /**
     * get board value
     */
    public function getBoardValue($boardId, $sType) {
        

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

        $sQuery = "SELECT * FROM board_value WHERE board_id = $boardId AND 
        type = '$sType' limit 1";

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

    /**
     * 댓글 삭제
     */
    public function removeComment($commentId)
    {
        $aResult = array(
            'result' => false,
            'msg' => ''
        );

        // 댓글 삭제 쿼리 작성
        $sQuery = "DELETE FROM board_value WHERE id = ? AND type = '".REPLY."'";
        $stmt = $this->oDataBase->prepare($sQuery);

        if ($stmt === false) {
            $aResult['msg'] = "쿼리 준비 중 오류가 발생했습니다.";
            return $aResult;
        }

        $stmt->bind_param("i", $commentId);
        $stmt->execute();

        if ($stmt->error) {
            $aResult['msg'] = "댓글 삭제 중 오류가 발생했습니다.";
        } else {
            $aResult['result'] = true;
        }

        $stmt->close();

        return $aResult;
    }

    /**
     * 게시글 수정
     */
    public function updateBoard($iBoardId, $aUpdateData)
    {
        $aResult = array(
            'result' => false,
            'msg' => ''
        );
    
        // 게시글 수정 쿼리 작성
        $sQuery = "UPDATE board 
                   SET title = '" . $this->oDataBase->real_escape_string($aUpdateData['title']) . "',
                       content = '" . $this->oDataBase->real_escape_string($aUpdateData['content']) . "'
                   WHERE id = '$iBoardId'";
    
        // 쿼리 실행
        if ($this->oDataBase->query($sQuery) === true) {
            // 성공시
            $aResult['result'] = true;
        } else {
            $aResult['msg'] = "게시글 수정 중 오류가 발생했습니다.";
        }
    
        return $aResult;
    }


    /**
     * 게시글 삭제
     */
    public function deleteBoard($boardId) {
        $aResult = array(
            'result' => false,
            'msg' => ''
        );

        // 게시글 삭제 쿼리 작성
        $sQuery = "DELETE FROM board WHERE id = '$boardId'";

        // 삭제 실행
        if ($this->oDataBase->query($sQuery) === true) {
            // 성공시
            $aResult['result'] = true;
            $aResult['msg'] = '게시글이 삭제되었습니다.';
        } else {
            $aResult['msg'] = '게시글 삭제 중 오류가 발생했습니다.';
        }

        return $aResult;
    }

    public function __destruct() {
        $this->oDataBase->close();
    }

}