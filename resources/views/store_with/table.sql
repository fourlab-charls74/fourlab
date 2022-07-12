
-- 오프라인 재고
CREATE TABLE `product_stock` (
                                 `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
                                 `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
                                 `qty` INT(11) DEFAULT NULL COMMENT '전체보유재고',
                                 `wqty` INT(11) DEFAULT NULL COMMENT '창고보유재고',
                                 `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
                                 `barcode` VARCHAR(50) DEFAULT NULL COMMENT '바코드',
                                 `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
                                 `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
                                 `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
                                 PRIMARY KEY (`goods_no`,`prd_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고';

-- 오프라인 재고(매장)
CREATE TABLE `product_stock_store` (
                                 `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
                                 `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
                                 `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '매장코드',
                                 `qty` INT(11) DEFAULT NULL COMMENT '재고',
                                 `wqty` INT(11) DEFAULT NULL COMMENT '보유재고',
                                 `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
                                 `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
                                 `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
                                 `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
                                 PRIMARY KEY (`goods_no`,`prd_cd`,`store_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 매장별';

-- 오프라인 재고(물류)
CREATE TABLE `product_stock_storage` (
                                 `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
                                 `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
                                 `storage_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '창고코드',
                                 `qty` INT(11) DEFAULT NULL COMMENT '재고',
                                 `wqty` INT(11) DEFAULT NULL COMMENT '보유재고',
                                 `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
                                 `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
                                 `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
                                 `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
                                 PRIMARY KEY (`goods_no`,`prd_cd`,`storage_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 물류별';


insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values
('P_STOCK_TYPE','10','매입','','','','Y','0','ceduce','본사_김용남',now(),now()),
('P_STOCK_TYPE','20','주문','','','','Y','0','ceduce','본사_김용남',now(),now()),
('P_STOCK_TYPE','11','반품','','','','Y','0','ceduce','본사_김용남',now(),now()),
('P_STOCK_TYPE','30','재고조정','','','','Y','0','ceduce','본사_김용남',now(),now());

-- 오프라인 출고
CREATE TABLE `product_stock_release` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `type` VARCHAR(50) DEFAULT NULL COMMENT '분류 - code : REL_TYPE (초도/판매분/요청분/일반 : F/S/R/G)',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '수령매장코드',
    `storage_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '창고코드',
    `state` INT(11) NOT NULL DEFAULT '0' COMMENT '상태(요청/접수/출고/입고(매장)/거부:10/20/30/40/-10)',
    `exp_dlv_day` VARCHAR(8) DEFAULT NULL COMMENT '출고예정일자',
    `rel_order` VARCHAR(30) DEFAULT NULL COMMENT '출고차수 - 출고예정일자 + code : REL_ORDER (01 - 25)',
    `comment` VARCHAR(255) DEFAULT NULL COMMENT '출고메모(거부사유 등)',
    `req_id` VARCHAR(50) DEFAULT NULL COMMENT '요청자',
    `req_rt` DATETIME DEFAULT NULL COMMENT '요청일시',
    `rec_id` VARCHAR(50) DEFAULT NULL COMMENT '접수자',
    `rec_rt` DATETIME DEFAULT NULL COMMENT '접수일시',
    `prc_id` VARCHAR(50) DEFAULT NULL COMMENT '처리자',
    `prc_rt` DATETIME DEFAULT NULL COMMENT '처리일시',
    `fin_id` VARCHAR(50) DEFAULT NULL COMMENT '완료자',
    `fin_rt` DATETIME DEFAULT NULL COMMENT '완료일시',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
    `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
    PRIMARY KEY (`idx`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 이동';

-- 오프라인 RT
CREATE TABLE `product_stock_rotation` (
  `type` VARCHAR(50) DEFAULT NULL COMMENT '분류',
  `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
  `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
  `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
  `qty` INT(11) DEFAULT NULL COMMENT '수량',
  `dep_store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '출고매장코드',
  `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '수령매장코드',
  `state` INT(11) NOT NULL DEFAULT '0' COMMENT '상태(요청/접수/출고(매장)/입고/거부:10/20/30/40/-10)',
  `exp_dlv_day` VARCHAR(8) DEFAULT NULL COMMENT '출고예정일자',
  `comment` VARCHAR(255) DEFAULT NULL COMMENT 'RT메모(거부사유 등)',
  `req_id` VARCHAR(50) DEFAULT NULL COMMENT '요청자',
  `req_rt` DATETIME DEFAULT NULL COMMENT '요청일시',
  `rec_id` VARCHAR(50) DEFAULT NULL COMMENT '접수자',
  `rec_rt` DATETIME DEFAULT NULL COMMENT '접수일시',
  `prc_id` VARCHAR(50) DEFAULT NULL COMMENT '처리자',
  `prc_rt` DATETIME DEFAULT NULL COMMENT '처리일시',
  `fin_id` VARCHAR(50) DEFAULT NULL COMMENT '완료자',
  `fin_rt` DATETIME DEFAULT NULL COMMENT '완료일시',
  `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
  `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
  PRIMARY KEY (`goods_no`,`prd_cd`,`store_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 이동';


-- 오프라인 재고(매장) 입출고
CREATE TABLE `product_stock_hst` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '매장코드',
    `type` VARCHAR(50) DEFAULT NULL COMMENT '분류',
    `cost` DECIMAL(10,0) DEFAULT NULL COMMENT '원가',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `invoice_no` VARCHAR(20) DEFAULT NULL COMMENT '송장번호',
    `stock_state_date` VARCHAR(8) DEFAULT NULL COMMENT '재고상태일시',
    `com_id` VARCHAR(20) DEFAULT NULL COMMENT '업체ID',
    `ord_opt_no` INT(11) DEFAULT NULL COMMENT '주문옵션번호',
    `uid` VARCHAR(50) DEFAULT NULL COMMENT '처리자',
    `unm` VARCHAR(50) DEFAULT NULL COMMENT '처리자명',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
    `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
    PRIMARY KEY (`idx`),
    KEY `goods_no` (`goods_no`,`goods_opt`),
    KEY `prd_cd` (`prd_cd`),
    KEY `invoice_no` (`invoice_no`),
    KEY `stock_state_date` (`stock_state_date`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 이력';

-- 입고
ALTER TABLE `bizest_smart`.`stock_product` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `brand`;

-- 주문에 '상품코드' 추가
ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `out_ord_no`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `out_ord_opt_no`;
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `tax_fee`;

-- 매장
CREATE TABLE `store` (
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
    `store_nm` varchar(100) NOT NULL COMMENT '매장명',
    `store_nm_s` varchar(50) NOT NULL COMMENT '매장명(약칭)',
    `store_type` varchar(30) NOT NULL COMMENT '매장구분 - code : store_type',
    `store_kind` varchar(30) NOT NULL COMMENT '매장종류 - code : store_kind',
    `store_area` varchar(30) NOT NULL COMMENT '지역코드 - code : store_area',
    `zipcode` varchar(7) DEFAULT NULL COMMENT '우편번호',
    `addr1` varchar(255) DEFAULT NULL COMMENT '주소1',
    `addr2` varchar(100) DEFAULT NULL COMMENT '주소2',
    `phone` varchar(15) DEFAULT NULL COMMENT '전화번호',
    `fax` varchar(15) DEFAULT NULL COMMENT '팩스번호',
    `mobile` varchar(15) DEFAULT NULL COMMENT '휴대폰번호',
    `manager_nm` varchar(30) DEFAULT NULL COMMENT '매니저명',
    `manager_mobile` varchar(15) DEFAULT NULL COMMENT '매니저 휴대폰',
    `email` varchar(100) DEFAULT NULL COMMENT '이메일',
    `fee` int(11) DEFAULT NULL COMMENT '기본수수료',
    `sale_fee` decimal(10,2) DEFAULT NULL COMMENT '판매수수료율',
    `md_manage_yn` char(1) DEFAULT 'Y' COMMENT '중간관리여부',
    `bank_no` varchar(30) DEFAULT NULL COMMENT '은행계좌번호',
    `bank_nm` varchar(30) DEFAULT NULL COMMENT '은행명',
    `depositor` varchar(50) DEFAULT NULL COMMENT '예금주',
    `deposit_cash` int(11) DEFAULT NULL COMMENT '매장보증금',
    `deposit_coll` int(11) DEFAULT NULL COMMENT '부동산담보',
    `loss_rate` decimal(10,2) DEFAULT NULL COMMENT '로스인정률',
    `sdate` varchar(30) DEFAULT NULL COMMENT '오픈일',
    `edate` varchar(30) DEFAULT NULL COMMENT '종료일',
    `use_yn` char(1) DEFAULT 'Y' COMMENT '매장사용유무',
    `ipgo_yn` char(1) DEFAULT 'Y' COMMENT '입고확인사용여부',
    `vat_yn` char(1) DEFAULT 'Y' COMMENT '부가세 사용',
    `biz_no` varchar(25) DEFAULT NULL COMMENT '사업자 - 등록번호',
    `biz_nm` varchar(50) DEFAULT NULL COMMENT '사업자 - 상호',
    `biz_ceo` varchar(30) DEFAULT NULL COMMENT '사업자 - 대표자명',
    `biz_zipcode` varchar(5) DEFAULT NULL COMMENT '사업자 - 우편번호',
    `biz_addr1` varchar(100) DEFAULT NULL COMMENT '사업자 - 주소1',
    `biz_addr2` varchar(100) DEFAULT NULL COMMENT '사업자 - 주소2',
    `biz_uptae` varchar(30) DEFAULT NULL COMMENT '사업자 - 업태',
    `biz_upjong` varchar(50) DEFAULT NULL COMMENT '사업자 - 업종',
    `manage_type` char(1) DEFAULT 'M' COMMENT '관리기준 - M:중간관리식, P:사입식',
    `exp_manage_yn` char(1) DEFAULT 'N' COMMENT '경비관리유무',
    `priority` varchar(30) DEFAULT NULL COMMENT '출고우선순위 - code : prority',
    `competitor_yn` char(1) DEFAULT 'N' COMMENT '동종업계정보입력',
    `pos_yn` char(1) DEFAULT 'Y' COMMENT 'POS 사용여부',
    `ostore_stock_yn` char(1) DEFAULT 'Y' COMMENT '타매장재고조회',
    `sale_dist_yn` char(1) DEFAULT 'Y' COMMENT '판매분배분여부',
    `rt_yn` char(1) DEFAULT 'Y' COMMENT '매장RT여부',
    `point_in_yn` char(1) DEFAULT 'N' COMMENT '적립금적립여부',
    `point_out_yn` char(1) DEFAULT 'N' COMMENT '적립금사용여부',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
    PRIMARY KEY (`store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `store_sales_projection` (
                         `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
                         `ym` varchar(6) NOT NULL COMMENT '년월',
                         `amt` bigint NOT NULL default '0' COMMENT '매출액',
                         `uid` VARCHAR(50) DEFAULT NULL COMMENT '처리자',
                         `unm` VARCHAR(50) DEFAULT NULL COMMENT '처리자명',
                         `rt` datetime DEFAULT NULL COMMENT '등록일',
                         `ut` datetime DEFAULT NULL COMMENT '수정일',
                         PRIMARY KEY (`store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- 창고
CREATE TABLE `storage` (
    `storage_cd` varchar(30) NOT NULL COMMENT '창고코드',
    `storage_nm` varchar(100) NOT NULL COMMENT '창고명칭',
    `storage_nm_s` varchar(50) NOT NULL COMMENT '창고명칭(약칭)',
    `zipcode` varchar(5) NOT NULL COMMENT '우편번호',
    `addr1` varchar(255) NOT NULL COMMENT '주소1',
    `addr2` varchar(100) DEFAULT NULL COMMENT '주소2',
    `phone` varchar(15) DEFAULT NULL COMMENT '전화번호',
    `fax` varchar(15) DEFAULT NULL COMMENT 'FAX번호',
    `ceo` varchar(30) DEFAULT NULL COMMENT '대표자명',
    `use_yn` char(1) DEFAULT NULL COMMENT '창고사용여부',
    `loss_yn` char(1) DEFAULT NULL COMMENT 'LOSS창고여부',
    `stock_check_yn` char(1) DEFAULT NULL COMMENT '매장재고조회여부',
    `default_yn` char(1) DEFAULT 'N' COMMENT '대표창고여부',
    `comment` VARCHAR(255) DEFAULT NULL COMMENT '창고설명',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '등록/수정아이디',
    PRIMARY KEY (`storage_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 동종업계
CREATE TABLE `competitor` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드 - store : store_cd',
    `competitor_cd` varchar(50) NOT NULL COMMENT '동종업계코드 - code : COMPETITOR',
    `concept` varchar(100) DEFAULT NULL COMMENT '컨셉',
    `item` varchar(100) DEFAULT NULL COMMENT '주아이템',
    `manager` varchar(30) DEFAULT NULL COMMENT '매니저',
    `sdate` varchar(10) DEFAULT NULL COMMENT '동종업계등록일',
    `edate` varchar(10) DEFAULT NULL COMMENT '폐점일',
    `use_yn` char(1) DEFAULT 'Y' COMMENT '사용',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일자',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8;


-- 판매 유형 관리
CREATE TABLE `sale_type` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `sale_kind` varchar(30) NOT NULL COMMENT '판매구분 - code : SALE_KIND',
    `sale_type_nm` varchar(100) NOT NULL COMMENT '판매유형명',
    `sale_apply` varchar(10) DEFAULT 'price' COMMENT '기준금액 : 판매가(price)/TAG가(tag)',
    `amt_kind` varchar(10) DEFAULT 'per' COMMENT '적용구분 : 할인율(per)/할인액(amt)',
    `sale_amt` int(11) DEFAULT NULL COMMENT '할인액',
    `sale_per` decimal(10,2) DEFAULT NULL COMMENT '할인율',
    `use_yn` char(1) DEFAULT 'Y' COMMENT '사용여부',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일자',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


-- 판매 유형 관리 - 매장
CREATE TABLE `sale_type_store` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `sale_type_cd` varchar(30) NOT NULL COMMENT '판매유형 idx',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
    `store_nm` varchar(100) NOT NULL COMMENT '매장명',
    `sdate` char(10) DEFAULT NULL COMMENT '시작일 (yyyy-mm-dd)',
    `edate` char(10) DEFAULT NULL COMMENT '종료일 (yyyy-mm-dd)',
    `use_yn` char(1) NOT NULL DEFAULT 'N' COMMENT '사용여부',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일자',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일자',
    PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 기존 테이블 컬럼 추가 시작
--

-- 브랜드에 '브랜드 단축코드' 추가
ALTER TABLE `bizest_smart`.`brand` ADD COLUMN `br_cd` VARCHAR(3) NULL COMMENT '단축코드' AFTER `brand_nm_eng`;

--
-- 기존 테이블 컬럼 추가 종료
--

--
-- 테이블 데이터 추가 시작
--
-- code_kind 데이터 추가 매장구분 : STORE_TYPE
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','[매장관리]매장구분','store type','Y','S','0','admin','본사_김용남',now(),now());


-- code 데이터 추가 매장구분 : STORE_TYPE
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','00','본사매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','07','편집샵',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','08','피엘라벤',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','10','사입매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','11','온라인',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','12','위탁대리점',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','13','행사매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_TYPE','15','브랜드스토어',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());


-- code_kind 데이터 추가 매장종류 : STORE_KIND
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','[매장관리]매장종류','store kind','Y','S','0','admin','본사_김용남',now(),now());





-- code 데이터 추가 매장종류 : STORE_KIND
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','02','온라인매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','03','아울렛매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','04','직영매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','05','위탁대리점',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','06','기타매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','07','본사매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_KIND','08','사입매장',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());


-- code_kind 데이터 추가 매장지역 : STORE_AREA
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','[매장관리]지역구분','store area','Y','S','0','admin','본사_김용남',now(),now());

-- code 데이터 추가 매장지역 : STORE_AREA
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','02','경기',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','03','인천',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','04','부산',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','05','대구',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','06','광주',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','07','대전',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','08','울산',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','09','강원',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','10','충남',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','11','충북',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','12','전남',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','13','전북',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','14','경남',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','15','경북',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','16','제주',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STORE_AREA','17','천안',NULL,NULL,NULL,'Y',NULL,'ceduce','본사_김용남',now(),now());


-- code_kind 데이터 추가 출고우선순위 : PRIORITY
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','[매장관리]출고우선순위','priority','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 출고우선순위 : PRIORITY
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','02','백화점B','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','03','백화점C','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','04','대리점A','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','05','대리점B','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','06','편집샵','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','07','퓨처사입','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','08','아울렛','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRIORITY','99','배분사용안함','','','','Y','0','ceduce','본사_김용남',now(),now());


-- code_kind 데이터 추가 동종업계 : COMPETITOR
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','[매장관리]동종업계','competitor','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 동종업계 : COMPETITOR
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','02','네파','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','03','노스페이스','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','04','코오롱','','','','Y','4','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','05','라푸마','','','','Y','5','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','06','k2','','','','Y','6','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','07','아이더','','','','Y','7','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','08','블랙야크','','','','Y','8','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','09','컬럼비아','','','','Y','9','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','10','에이글','','','','Y','10','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','11','마운틴하드웨어','','','','Y','11','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','12','트렉스타','','','','Y','12','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','13','몽벨','','','','Y','13','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','14','더도어','','','','Y','14','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','15','픽퍼포먼스','','','','Y','15','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','16','오프로드','','','','Y','16','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','17','노티카','','','','Y','17','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','18','윌리엄스버그','','','','Y','18','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','19','하그로브스','','','','Y','19','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','20','아크테릭스','','','','Y','20','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','21','몬츄라','','','','Y','21','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','22','살로몬','','','','Y','22','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','23','머렐','','','','Y','23','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','24','빈폴스포츠','','','','Y','24','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','25','스노우피크','','','','Y','25','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','26','디스커버리','','','','Y','26','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','27','센터폴','','','','Y','27','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','28','오스프리','','','','Y','28','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','29','마무트','','','','Y','29','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','30','페리노','','','','Y','30','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','31','마모트','','','','Y','31','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','32','파타고니아','','','','Y','32','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','33','슈퍼콤마비','','','','Y','33','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','34','플라넷B','','','','Y','34','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','35','어드바이저리','','','','Y','35','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','36','GR-8','','','','Y','36','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','37','리바이스','','','','Y','37','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','38','게스','','','','Y','38','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','39','버커루','','','','Y','39','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','40','CK진','','','','Y','40','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','41','힐피거데님','','','','Y','41','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','42','디젤','','','','Y','42','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','43','칼하트','','','','Y','43','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','44','노비스','','','','Y','44','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','45','하이드로겐','','','','Y','45','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','46','무스너클','','','','Y','46','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','47','플랙진','','','','Y','47','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','48','홀하우스','','','','Y','48','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','49','써스데이아일랜드','','','','Y','49','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','50','데님바','','','','Y','50','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','51','지프','','','','Y','51','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','52','노스페이스영','','','','Y','52','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','53','MLB','','','','Y','53','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','54','펠틱스','','','','Y','54','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','55','카이아크만','','','','Y','55','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','56','TI포맨','','','','Y','56','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','57','오클리','','','','Y','57','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','58','엘트레일','','','','Y','58','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','59','아웃도어+1','','','','Y','59','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','60','홀라인','','','','Y','60','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','61','살레와','','','','Y','61','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','62','그레고리','','','','Y','62','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','63','내셔널지오그래픽','','','','Y','63','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','64','웨스트우드','','','','Y','64','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','65','펜필드','','','','Y','65','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','66','도이터','','','','Y','66','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','67','말로야','','','','Y','67','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','68','코닥','','','','Y','68','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','69','나우','','','','Y','69','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','70','잔카','','','','Y','70','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','71','콜핑','','','','Y','71','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','72','클라터뮤젠','','','','Y','72','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','73','제로그램','','','','Y','73','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','74','CNN어패럴','','','','Y','74','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','75','카라반','','','','Y','75','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('COMPETITOR','76','홀루바','','','','Y','76','ceduce','본사_김용남',now(),now());


-- code_kind 데이터 추가 판매구분 : SALE_KIND
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','[매장관리]판매구분','sale_kind','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 판매구분 : SALE_KIND
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','02','쿠폰판매(10%)','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','03','쿠폰판매(15%)','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','04','쿠폰판매(20%)','','','','Y','4','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','05','5% 할인','','','','Y','5','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','06','10%할인','','','','Y','6','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','07','20%할인','','','','Y','7','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','08','30%할인','','','','Y','8','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','09','브랜드데이10%','','','','Y','9','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','10','임직원20%할인(당월 매출 10%)','','','','Y','10','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 출고상태 : REL_TYPE
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_TYPE','[매장관리]출고상태','rel_type','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 출고상태 : REL_TYPE
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_TYPE','F','초도출고','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_TYPE','S','판매분출고','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_TYPE','R','요청분출고','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_TYPE','G','일반출고','','','','Y','4','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 출고차수 : REL_ORDER
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','[매장관리]출고차수','rel_order','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 출고차수 : REL_ORDER
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','01','01','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','02','02','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','03','03','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','04','04','','','','Y','4','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','05','05','','','','Y','5','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','06','06','','','','Y','6','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','07','07','','','','Y','7','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','08','08','','','','Y','8','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','09','09','','','','Y','9','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','10','10','','','','Y','10','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','11','11','','','','Y','11','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','12','12','','','','Y','12','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','13','13','','','','Y','13','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','14','14','','','','Y','14','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','15','15','','','','Y','15','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','16','16','','','','Y','16','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','17','17','','','','Y','17','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','18','18','','','','Y','18','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','19','19','','','','Y','19','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','20','20','','','','Y','20','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','21','21','','','','Y','21','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','22','22','','','','Y','22','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','23','23','','','','Y','23','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','24','24','','','','Y','24','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('REL_ORDER','25','25','','','','Y','25','ceduce','본사_김용남',now(),now());

--
-- 테이블 데이터 추가 종료
--
