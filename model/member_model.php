<?php

require_once "../const/common.php";

class MemberModel {
    
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
     * 해당하는 회원 id에 대응되는 member 정보 반환
     */
    public function selectMemberByMemberId($sMemberId) {

        $aReturn = array();

        $sQuery = "select * from member where user_id = '$sMemberId'";

        $result = $this->oDataBase->query($sQuery);

        // 결과값이 있는 경우
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
             
        } 


        return $aReturn;
    }


    /**
     * 회원 가입시 member 테이블에 회원 row 추가
     */
    public function insertMember ($aInsert) {
        $aResult = array(
            'result' => true
        );

        $sQuery= "INSERT INTO member (user_id, password, member_grade, nickname, level, profile_path, game_nickname) 
            VALUES ('".$aInsert['user_id']."', '".$aInsert['password']."', '".$aInsert['member_grade']."', '".$aInsert['nickname']."', '".$aInsert['level']."', '".$aInsert['profile_path']."', '".$aInsert['character_name']."')";

            // insert 성공시
            if ($this->oDataBase->query($sQuery) === true) {
                if (session_status() === PHP_SESSION_NONE) {   
                    session_start();
                 }
                
                // 세션 정보에 저장
                $_SESSION["user_id"] = $aInsert['user_id'];
                $_SESSION["nickname"] = $aInsert['nickname'];
                $_SESSION['profile_picture'] = $aInsert['profile_path'];

                return $aResult;
            } else { // 실패시
                return array(
                    'result' => false,
                    'msg' => $this->oDataBase->error
                );
            }
    }

    /**
     * member value 조회
     */
    public function getMemberValueByIdAndType ($sUserId, $sType) {

        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );
    
        
        $sQuery = "SELECT *  
                   FROM member_value
                   WHERE user_id = '$sUserId' and type = '$sType'";
    
        // 쿼리 실행
        $oResult = $this->oDataBase->query($sQuery);
    
        // 결과값이 있는 경우
        $aReturn = array();
        if ($oResult && $oResult->num_rows > 0) {
            // 순회해서 return 값 세팅
            while($aRow = $oResult->fetch_assoc()) {
                $aReturn[] = $aRow['value'];
            }
            $aResult['result'] = true;
            $aResult['data'] = $aReturn;
        } else {
            $aResult['msg'] = "관련 정보가 없습니다.";
        }
    
        return $aResult;

    }

    /**
     * member value 전체조회
     */
    public function getAllMemberValueByIdAndType ($sUserId, $sType) {

        $aResult = array(
            'result' => false,
            'msg' => '',
            'data' => array()
        );
    
        
        $sQuery = "SELECT *  
                   FROM member_value
                   WHERE user_id = '$sUserId' and type = '$sType'";
    
        // 쿼리 실행
        $oResult = $this->oDataBase->query($sQuery);
    
        // 결과값이 있는 경우
        $aReturn = array();
        if ($oResult && $oResult->num_rows > 0) {
            // 순회해서 return 값 세팅
            while($aRow = $oResult->fetch_assoc()) {
                $aReturn[] = $aRow;
            }
            $aResult['result'] = true;
            $aResult['data'] = $aReturn;
        } else {
            $aResult['msg'] = "관련 정보가 없습니다.";
        }
    
        return $aResult;

    }


    /**
     * member_value 테이블에 insert
     */
    public function insertMemberValue($userId, $type, $value) {
        $result = array(
            'result' => false,
            'msg' => ''
        );

        $query = "INSERT INTO member_value (user_id, type, value) VALUES (?, ?, ?)";
        $stmt = $this->oDataBase->prepare($query);

        if ($stmt === false) {
            $result['msg'] = "쿼리 준비 중 오류가 발생했습니다.";
            return $result;
        }

        $stmt->bind_param("sss", $userId, $type, $value);
        $stmt->execute();

        if ($stmt->error) {
            $result['msg'] = "데이터 삽입 중 오류가 발생했습니다.";
        } else {
            $result['result'] = true;
        }

        $stmt->close();

        return $result;
    }


    /**
     * 게임 닉네임으로 해당하는 member 정보 반환
     */
    public function selectMemberByGameName($gameName) {
        $aReturn = array();

        $sQuery = "SELECT * FROM member WHERE game_nickname = '$gameName'";
        $result = $this->oDataBase->query($sQuery);

        // 결과값이 있는 경우
        if ($result->num_rows > 0) {
            // 값 리턴
            $aReturn = $result->fetch_assoc();
            
        }

        return empty($aReturn) ? array() : $aReturn;
    }


    /**
     * 방명록을 추가하는 메서드
     */
    public function insertGuestbook($toUserId, $fromUserId, $message) {
        $result = array(
            'result' => false,
            'msg' => ''
        );

        // 인수 체크
        if (empty($toUserId) || empty($fromUserId) || empty($message)) {
            $result['msg'] = '필수값이 누락되었습니다.';
            return $result;
        }

        // member_value 테이블에 방명록 추가
        $query = "INSERT INTO member_value (user_id, type, value, mapping_id) 
                VALUES (?, 'comment', ?, ?)";
        $stmt = $this->oDataBase->prepare($query);

        if ($stmt === false) {
            $result['msg'] = "쿼리 준비 중 오류가 발생했습니다.";
            return $result;
        }

        $stmt->bind_param("sss", $toUserId, $message, $fromUserId);
        $stmt->execute();

        if ($stmt->error) {
            $result['msg'] = "방명록을 추가하는 중 오류가 발생했습니다.";
        } else {
            $result['result'] = true;
            $result['msg'] = "방명록이 성공적으로 추가되었습니다.";
            $result['data'] = array(
                'id' => $stmt->insert_id,
                'comment' => $message
            ); 
        }

        $stmt->close();

        return $result;
    }

    /**
     * member table likes(좋아요) 컬럼 업데이트
     */
    public function updateMemberLike($userGameName) {
        $result = array(
            'result' => false,
            'msg' => ''
        );
    
        // 입력값 검증
        if (empty($userGameName)) {
            $result['msg'] = '유효하지 않은 사용자입니다.';
            return $result;
        }
    
        // likes 컬럼 업데이트 쿼리 작성
        $query = "UPDATE member SET likes = likes + 1 WHERE game_nickname = ?";
        $stmt = $this->oDataBase->prepare($query);
    
        if ($stmt === false) {
            $result['msg'] = "쿼리 준비 중 오류가 발생했습니다.";
            return $result;
        }
    
        $stmt->bind_param("s", $userGameName);
        $stmt->execute();
    
        if ($stmt->error) {
            $result['msg'] = "좋아요 업데이트 중 오류가 발생했습니다.";
        } else {
            $result['result'] = true;
        }
    
        $stmt->close();
    
        return $result;

    }


    public function __destruct() {
        // db연결 종료
        $this->oDataBase->close();
    }
}