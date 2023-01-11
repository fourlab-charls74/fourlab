
-- 매장관리
CREATE TABLE `zzz_mig_xmd_store` (
                                     `업체명` VARCHAR(100) DEFAULT NULL,
                                     `매장코드` VARCHAR(100) DEFAULT NULL,
                                     `매장명` VARCHAR(100) DEFAULT NULL,
                                     `거래구분` VARCHAR(100) DEFAULT NULL,
                                     `대표전화` VARCHAR(100) DEFAULT NULL,
                                     `핸드폰번호` VARCHAR(100) DEFAULT NULL,
                                     `FAX번호` VARCHAR(100) DEFAULT NULL,
                                     `우편번호` VARCHAR(100) DEFAULT NULL,
                                     `주소` VARCHAR(100) DEFAULT NULL,
                                     `개장일자` VARCHAR(100) DEFAULT NULL,
                                     `폐점일자` VARCHAR(100) DEFAULT NULL,
                                     `매니저명` VARCHAR(100) DEFAULT NULL,
                                     `매니저 시작일자` VARCHAR(100) DEFAULT NULL,
                                     `매니저 종료일자` VARCHAR(100) DEFAULT NULL,
                                     `매니저보증금` VARCHAR(100) DEFAULT NULL,
                                     `매니저수수료(정상)` VARCHAR(100) DEFAULT NULL,
                                     `매니저수수료(행사)` VARCHAR(100) DEFAULT NULL,
                                     `보증금(현금)` VARCHAR(100) DEFAULT NULL,
                                     `보증금(담보)` VARCHAR(100) DEFAULT NULL,
                                     `인테리어(비용)` VARCHAR(100) DEFAULT NULL,
                                     `인테리어(부담)` VARCHAR(100) DEFAULT NULL,
                                     `기본수수료` VARCHAR(100) DEFAULT NULL,
                                     `판매수수료율` VARCHAR(100) DEFAULT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8;

LOAD DATA LOCAL INFILE 'C:\\Users\\steve\\Desktop\\store_mig\\store_list.txt' INTO TABLE `bizest_smart`.`zzz_mig_xmd_store` FIELDS ESCAPED BY '\\' TERMINATED BY '\t' OPTIONALLY ENCLOSED BY '"' LINES TERMINATED BY '\n'
    (`업체명`, `매장코드`, `매장명`, `거래구분`, `대표전화`, `핸드폰번호`, `FAX번호`, `우편번호`, `주소`, `개장일자`, `폐점일자`, `매니저명`, `매니저 시작일자`, `매니저 종료일자`, `매니저보증금`, `매니저수수료(정상)`, `매니저수수료(행사)`, `보증금(현금)`, `보증금(담보)`, `인테리어(비용)`, `인테리어(부담)`, `기본수수료`, `판매수수료율`)


INSERT INTO `store` (
    store_type,
    store_cd,
    store_nm,
    store_kind,
    phone,
    mobile,
    fax,
    zipcode,
    addr1,
    sdate,
    edate,
    manager_nm,
    fee,
    sale_fee,
    reg_date,mod_date
)
SELECT
    CASE `업체명`
        WHEN '편집샵' THEN '07'
        WHEN '피엘라벤' THEN '08'
        WHEN '본사매장' THEN '00'
        WHEN '사입매장' THEN '10'
        WHEN '온라인' THEN '11'
        WHEN '위탁대리점' THEN '12'
        WHEN '행사매장' THEN '13'
        WHEN '브랜드스토어' THEN '14'
        ELSE `업체명`
        END AS store_type,
    `매장코드`,
    `매장명`,
    CASE `거래구분`
        WHEN '온라인매장' THEN '02'
        WHEN '아울렛매장' THEN '03'
        WHEN '직영매장' THEN '04'
        WHEN '위탁대리점' THEN '05'
        WHEN '기타매장' THEN '06'
        WHEN '본사매장' THEN '07'
        WHEN '사입매장' THEN '08'
        END AS store_kind,
    `대표전화`,
    `핸드폰번호`,
    `FAX번호`,
    `우편번호`,
    `주소`,
    REPLACE(`개장일자`,'/',''),
    REPLACE(`폐점일자`,'/',''),
    `매니저명`,
--  `매니저 시작일자`,
--  `매니저 종료일자`,
--  `매니저보증금`,
--  `매니저수수료(정상)`,
--  `매니저수수료(행사)`,
--  `보증금(현금)`,
--  `보증금(담보)`,
--  `인테리어(비용)`,
--  `인테리어(부담)`,
    `기본수수료`,
    `판매수수료율`,
    NOW() AS reg_date,
    NOW() AS mod_date
FROM `bizest_smart`.`zzz_mig_xmd_store`

