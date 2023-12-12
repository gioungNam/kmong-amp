<?php

/**
 * mysql 접속 정보 상수
 */

const MYSQL_HOST = "localhost";

const MYSQL_PORT = 3306;

const MYSQL_USER = "root";

const MYSQL_PASSWORD = "";

const MYSQL_DATABASE = "project";

/**
 * 회원 eav 관련 상수
 */


// 댓글
const REPLY = "reply";

// 좋아요
const LIKES = "likes";


class BoardConst {

    /**
     * 정의된 게시판 type
     */
    public static function getBoardType() {
        return array(
            'notice',
            'free',
            'img',
            'inquiry',
            'search'
        );
    }

    /**
     * board value type
     */
    public static function getBoardValueType() {
        return array(
            REPLY
        );

    }

    /**
     * 게시판 type을 name으로 변환
     */
    public static function convertTypeToName(String $type) {
    
        $aConvertInfo = array(
            'notice' => '공지 사항',
            'free' => '자유게시판', 
            'img' => '커마게시판',
            'inquiry' => '문의',
            'search' => '캐릭터 검색'
        );

        if (array_key_exists($type, $aConvertInfo)) {
            return $aConvertInfo[$type];
        } 

        return '';
    }
}
