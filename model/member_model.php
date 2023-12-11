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
            // 순회해서 return 값 세팅
            while($aRow = $result->fetch_assoc()) {
                foreach ($aRow as $sKey => $mValue) {
                    $aReturn[$sKey] = $mValue;
                }
            }
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


    public function __destruct() {
        // db연결 종료
        $this->oDataBase->close();
    }
}