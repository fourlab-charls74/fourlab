-- 상품정보 ( 원부자재 상품 포함 )
CREATE TABLE `product` (
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `prd_nm` VARCHAR(100) NOT NULL COMMENT '상품명',
    `prd_nm_eng` VARCHAR(100) NOT NULL COMMENT '상품명(영문)',
    `style_no` VARCHAR(50) NOT NUll COMMENT '스타일 넘버',
    `tag_price` int(11) COMMENT 'tag가',
    `price` int(11) NOT NULL COMMENT '판매가',
    `wonga` int(11) NOT NULL COMMENT '원가',
    `type` varchar(1) DEFAULT 'N' COMMENT '구분:일반(N),부자재(S),사은품(G)',
    `com_id` VARCHAR(30) NOT NULL COMMENT '공급업체',
    `unit` varchar(30) NOT NULL COMMENT '단위 code - prd_cd_unit',
    `match_yn` char(1) NOT NULL DEFAULT 'N' COMMENT '매칭여부',
    `use_yn` char(1) DEFAULT 'Y' COMMENT '사용여부',
    `rt` datetime NOT NULL COMMENT '등록일',
    `ut` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) NOT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 원부자재 상품 코드
CREATE TABLE `product_code` (
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `goods_no` int(11) COMMENT '상품번호',
    `goods_opt` varchar(100) COMMENT '상품옵션명',
    `brand` varchar(2) NOT NULL COMMENT '브랜드 code - prd_cd_brand, prd_material_type',
    `year` varchar(3) NOT NULL COMMENT '년도 code - prd_cd_year',
    `season` char(1) NOT NULL COMMENT '시즌 code - prd_cd_season',
    `gender` char(1) NOT NULL COMMENT '성별 code - prd_cd_gender',
    `item` varchar(3) NOT NULL COMMENT '아이템 code - prd_cd_item',
    `opt` varchar(3) NOT NULL COMMENT '품목 code - prd_cd_opt, prd_material_opt',
    `seq` int(2) unsigned zerofill NOT NULL COMMENT '상품코드 순서차수',
    `color` varchar(3) NOT NULL COMMENT '컬러옵션 code - prd_cd_color',
    `size` varchar(4) NOT NULL COMMENT '사이즈옵션 code - prd_cd_size',
    `type` varchar(1) DEFAULT 'N' COMMENT '구분:일반(N),부자재(S),사은품(G)',
    `rt` datetime NOT NULL COMMENT '등록일',
    `ut` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) NOT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 원부자재 상품 이미지
CREATE TABLE `product_image` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `seq` int(2) unsigned zerofill NOT NULL COMMENT '상품코드 순서차수',
    `img_url` varchar(255) COMMENT '이미지 주소',
    `rt` datetime NOT NULL COMMENT '등록일',
    `ut` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) NOT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`),
    KEY `idx_prdcd` (`prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 상품 입고/반품 마스터
CREATE TABLE `product_stock_order` (
  `stock_no` int(11) NOT NULL AUTO_INCREMENT COMMENT '입고번호',
  `stock_date` varchar(8) NOT NULL COMMENT '입고일자',
  `invoice_no` varchar(20) DEFAULT NULL COMMENT '송장번호',
  `bl_no` varchar(20) DEFAULT NULL COMMENT '통관번호(B/L No)',
  `stock_type` varchar(1) NOT NULL DEFAULT 'G' COMMENT '입고구분 - 일괄/발주:A,개별:G',
  `area_type` varchar(1) NOT NULL DEFAULT 'D' COMMENT '지역 - 해외:O,국내:D',
  `com_id` varchar(20) NOT NULL COMMENT '업체',
  `item` varchar(20) NOT NULL COMMENT '품목',
  `currency_unit` varchar(3) DEFAULT NULL COMMENT '화폐단위',
  `exchange_rate` decimal(10,2) DEFAULT NULL COMMENT '환율',
  `tariff_amt` int(11) DEFAULT NULL COMMENT '관세총액',
  `tariff_rate` decimal(10,2) DEFAULT NULL COMMENT '관세율',
  `freight_amt` int(11) DEFAULT NULL COMMENT '운임비',
  `freight_rate` decimal(10,2) DEFAULT NULL COMMENT '운임율',
  `custom_amt` int(11) DEFAULT NULL COMMENT '신고금액',
  `custom_tax` int(11) DEFAULT NULL COMMENT '통관비',
  `custom_tax_rate` decimal(10,2) DEFAULT NULL COMMENT '통관세율',
  `state` int(11) DEFAULT NULL COMMENT '상태(입고대기(10)/입고처리중(20)/입고완료(30)/원가확정(40)/입고취소(-10)',
  `loc` varchar(50) DEFAULT NULL COMMENT '위치',
  `opts` mediumtext COMMENT '옵션',
  `req_id` VARCHAR(50) DEFAULT NULL COMMENT '등록 id',
  `req_rt` DATETIME DEFAULT NULL COMMENT '등록일자',
  `prc_id` VARCHAR(50) DEFAULT NULL COMMENT '처리중 id',
  `prc_rt` DATETIME DEFAULT NULL COMMENT '처리중일자',
  `fin_id` VARCHAR(50) DEFAULT NULL COMMENT '완료 id',
  `fin_rt` DATETIME DEFAULT NULL COMMENT '완료일자',
  `cfm_id` VARCHAR(50) DEFAULT NULL COMMENT '원가확정 id',
  `cfm_rt` DATETIME DEFAULT NULL COMMENT '원가확정일자',
  `rej_id` VARCHAR(50) DEFAULT NULL COMMENT '취소 id',
  `rej_rt` DATETIME DEFAULT NULL COMMENT '취소일자',
  `ut` datetime DEFAULT NULL COMMENT '최근수정일',
  PRIMARY KEY (`stock_no`),
  KEY `buy_ord_no` (`invoice_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 상품 입고/반품 상품
CREATE TABLE `product_stock_order_product` (
  `stock_prd_no` int(11) NOT NULL AUTO_INCREMENT COMMENT '입고 상품 일련번호',
  `stock_no` int(11) NOT NULL COMMENT '입고번호',
  `invoice_no` varchar(50) NOT NULL COMMENT '송장번호',
  `com_id` varchar(20) NOT NULL COMMENT '업체',
  `item` varchar(20) NOT NULL COMMENT '품목',
  `brand` varchar(100) NOT NULL COMMENT '브랜드',
  `prd_cd` varchar(50) DEFAULT NULL COMMENT '상품코드',
  `style_no` varchar(100) DEFAULT NULL COMMENT '스타일넘버',
  `goods_no` int(11) NOT NULL COMMENT '상품번호',
  `goods_sub` int(11) NOT NULL COMMENT '상품번호(보조)',
  `opt_kor` varchar(100) DEFAULT NULL COMMENT '옵션(한국)',
  `exp_qty` int(11) DEFAULT NULL COMMENT '수량(입고예정)',
  `qty` int(11) DEFAULT NULL COMMENT '수량(입고확정)',
  `unit_cost` decimal(10,2) DEFAULT NULL COMMENT '단가',
  `prd_tariff_rate` decimal(10,2) DEFAULT NULL COMMENT '상품당 관세율',
  `cost_notax` decimal(10,0) DEFAULT NULL COMMENT '원가(VAT별도)',
  `total_cost` decimal(10,0) DEFAULT NULL COMMENT '총원가',
  `cost` decimal(10,0) DEFAULT NULL COMMENT '원가',
  `state` int(11) DEFAULT NULL COMMENT '상태(입고대기(10)/입고처리중(20)/입고완료(30)/원가확정(40)/입고취소(-10)',
  `stock_date` varchar(8) DEFAULT NULL COMMENT '입고일자',
  `id` varchar(8) DEFAULT NULL COMMENT '작성자',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '최근수정일',
  PRIMARY KEY (`stock_prd_no`),
  KEY `stock_no` (`stock_no`),
  KEY `invoice_no` (`invoice_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 원부자재 상품 입고/반품 마스터
CREATE TABLE `sproduct_stock_order` (
    `prd_ord_no` varchar(50) NOT NULL COMMENT '입고송장번호/반품번호',
    `kind` varchar(10) NOT NULL COMMENT '구분:in(입고), out(반품)',
    `prd_ord_date` varchar(8) NOT NULL COMMENT '일자',
    `prd_ord_type` char(1) COMMENT '입고구분:일반(N),부자재(S),사은품(G)',
    `com_id` varchar(30) NOT NULL COMMENT '공급업체',
    `state` varchar(5) DEFAULT 10 COMMENT '상태:입고대기(10),입고처리중(20),입고완료(30),반품대기(-10),반품처리중(-20),반품완료(-30)',
    `rt` datetime NOT NULL COMMENT '등록일',
    `ut` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) NOT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`prd_ord_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 원부자재 상품 입고/반품 상품
CREATE TABLE `sproduct_stock_order_product` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `prd_ord_no` varchar(50) NOT NULL COMMENT '입고송장번호/반품번호',
    `com_id` varchar(30) NOT NULL COMMENT '공급업체',
    `state` varchar(5) DEFAULT 10 COMMENT '상태:입고대기(10),입고처리중(20),입고완료(30),반품대기(-10),반품처리중(-20),반품완료(-30)',
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `prd_nm` VARCHAR(100) NOT NULL COMMENT '상품명',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `price` INT(11) DEFAULT NULL COMMENT '단가',
    `wonga` INT(11) DEFAULT NULL COMMENT '원가',
    `req_id` VARCHAR(50) DEFAULT NULL COMMENT '등록',
    `req_rt` DATETIME DEFAULT NULL COMMENT '등록일자',
    `prc_id` VARCHAR(50) DEFAULT NULL COMMENT '처리중',
    `prc_rt` DATETIME DEFAULT NULL COMMENT '처리중일자',
    `fin_id` VARCHAR(50) DEFAULT NULL COMMENT '완료',
    `fin_rt` DATETIME DEFAULT NULL COMMENT '완료일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) NOT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`),
    KEY `idx_prdordno` (`prd_ord_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 오프라인 재고
CREATE TABLE `product_stock` (
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `wonga` int(11) not null comment '최근원가',
    `qty_wonga` bigint(11) not null comment '총원가',
    `in_qty` INT(11) DEFAULT NULL COMMENT '입고수량',
    `out_qty` INT(11) DEFAULT NULL COMMENT '출고수량',
    `qty` INT(11) DEFAULT NULL COMMENT '보유재고',
    `wqty` INT(11) DEFAULT NULL COMMENT '창고보유재고',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `barcode` VARCHAR(50) DEFAULT NULL COMMENT '바코드',
    `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
    `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
    PRIMARY KEY (`prd_cd`),
    KEY `idx_goodsno` (`goods_no`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고';

-- 오프라인 재고(매장)
CREATE TABLE `product_stock_store` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Identify',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '매장코드',
    `qty` INT(11) DEFAULT NULL COMMENT '재고',
    `wqty` INT(11) DEFAULT NULL COMMENT '보유재고',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
    `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
    PRIMARY KEY (`idx`),
    KEY `idx_prdcd` (`prd_cd`),
    KEY `idx_storecd` (`store_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 매장별';

-- 오프라인 재고(물류)
CREATE TABLE `product_stock_storage` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Identify',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `storage_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '창고코드',
    `qty` INT(11) DEFAULT NULL COMMENT '재고',
    `wqty` INT(11) DEFAULT NULL COMMENT '보유재고',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
    `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
    PRIMARY KEY (`idx`),
    KEY `idx_prdcd` (`prd_cd`),
    KEY `idx_storagecd` (`storage_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 물류별';

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
    `rel_order` VARCHAR(30) DEFAULT NULL COMMENT '출고차수 - 출고예정일자 + "-" + 출고구분 + code : REL_ORDER (01 - 10)',
    `req_comment` VARCHAR(255) DEFAULT NULL COMMENT '요청메모 (매장메모)',
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
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품출고';

-- 오프라인 RT
CREATE TABLE `product_stock_rotation` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `type` VARCHAR(50) DEFAULT NULL COMMENT '분류',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `dep_store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '출고매장코드',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '수령매장코드',
    `state` INT(11) NOT NULL DEFAULT '0' COMMENT '상태(요청/접수/출고(매장)/입고/거부:10/20/30/40/-10)',
    `exp_dlv_day` VARCHAR(8) DEFAULT NULL COMMENT '출고예정일자',
    `req_comment` VARCHAR(255) DEFAULT NULL COMMENT 'RT요청메모',
    `rec_comment` VARCHAR(255) DEFAULT NULL COMMENT 'RT접수메모(거부사유 등)',
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
    `del_yn` CHAR(1) DEFAULT 'N' COMMENT '삭제여부',
    PRIMARY KEY (`idx`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 이동';

-- 오프라인 재고(매장) 입출고
CREATE TABLE `product_stock_hst` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
    `prd_cd` VARCHAR(50) NOT NULL COMMENT '상품코드',
    `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
    `location_cd` VARCHAR(30) NOT NULL COMMENT '매장코드 or 창고코드',
    `location_type` VARCHAR(10) NOT NULL COMMENT '매장(STORE) / 창고(STORAGE)',
    `type` VARCHAR(30) DEFAULT NULL COMMENT '분류 - code: product_stock_type', -- (입고(1) / 주문(2) / 교환(5) / 환불(6) / 주문취소(7) / 상품반품(9) / 반품(11) / LOSS(14) / RT(15) / 상품이동(16) / 출고(17))
    `price` INT(11) DEFAULT NULL COMMENT '판매가',
    `wonga` DECIMAL(10,0) DEFAULT NULL COMMENT '원가 - (수정해야함)',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `invoice_no` VARCHAR(20) DEFAULT NULL COMMENT '송장번호',
    `stock_state_date` VARCHAR(8) DEFAULT NULL COMMENT '재고상태일시',
    `com_id` VARCHAR(20) DEFAULT NULL COMMENT '업체ID',
    `ord_opt_no` INT(11) DEFAULT NULL COMMENT '주문옵션번호',
    `comment` VARCHAR(100) DEFAULT NULL COMMENT '메모',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
    `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
    `admin_id` VARCHAR(50) DEFAULT NULL COMMENT '처리자',
    `admin_nm` VARCHAR(50) DEFAULT NULL COMMENT '처리자명',
    PRIMARY KEY (`idx`),
    KEY `goods_no` (`goods_no`,`goods_opt`),
    KEY `prd_cd` (`prd_cd`),
    KEY `invoice_no` (`invoice_no`),
    KEY `stock_state_date` (`stock_state_date`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 이력';

-- 매장
CREATE TABLE `store` (
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
    `store_nm` varchar(100) NOT NULL COMMENT '매장명',
    `store_nm_s` varchar(50) NOT NULL COMMENT '매장명(약칭)',
    `store_type` varchar(30) NOT NULL COMMENT '매장구분 - code : store_type',
    `store_kind` varchar(30) NOT NULL COMMENT '매장종류 - code : store_kind',
    `grade_cd` varchar(2) NOT NULL COMMENT '매장등급 - code : store_grade : grade_cd',
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
    `map_code` varchar(100) COMMENT '맵 코드',
    `open_month_stock` varchar(1) DEFAULT 'N' COMMENT '오픈 후 한 달 재고보기 제외여부',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
    PRIMARY KEY (`store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장 목표
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 동종업계 매출관리
CREATE TABLE `competitor_sale` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '동종업계 매출 일련번호',
  `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
  `competitor_cd` int(11) NOT NULL COMMENT '동종업계 번호 - competitor : idx',
  `sale_date` varchar(10) NOT NULL COMMENT '매출일자 (yyyy-mm-dd)',
  `sale_amt` int(11) NOT NULL COMMENT '동종업계 매출액',
  `admin_id` varchar(50) DEFAULT NULL COMMENT '작성자',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '최근 수정일',
  PRIMARY KEY (`idx`),
  KEY `idx` (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- 매장마진관리
CREATE TABLE `store_fee` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드 - store : store_cd',
    `pr_code` varchar(30) NOT NULL COMMENT '행사코드 - code : PR_CODE',
    `store_fee` decimal(10,2) DEFAULT NULL COMMENT '매장수수료',
    `manager_fee` decimal(10,2) DEFAULT NULL COMMENT '중간관리수수료',
    `sdate` varchar(10) DEFAULT NULL COMMENT '시작일',
    `edate` varchar(10) DEFAULT NULL COMMENT '종료일',
    `comment` varchar(100) DEFAULT NULL COMMENT '메모',
    `use_yn` char(1) DEFAULT 'Y' COMMENT '사용',
    `reg_date` datetime DEFAULT NULL COMMENT '등록일자',
    `mod_date` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장등급관리
CREATE TABLE `store_grade` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `seq` INT(11) NOT NULL COMMENT '정렬순서',
    `grade_cd` VARCHAR(2) NOT NULL COMMENT '등급코드',
    `name` VARCHAR(30) NOT NULL COMMENT '등급명',
    `sdate` VARCHAR(7) DEFAULT NULL COMMENT '시작월',
    `edate` VARCHAR(7) DEFAULT NULL COMMENT '종료월',
    `amt1` INT(11) DEFAULT NULL COMMENT '금액1',
    `fee1` DECIMAL(5,2) DEFAULT NULL COMMENT '수수료1',
    `amt2` INT(11) DEFAULT NULL COMMENT '금액2',
    `fee2` DECIMAL(5,2) DEFAULT NULL COMMENT '수수료2',
    `amt3` INT DEFAULT NULL COMMENT '금액3',
    `fee3` DECIMAL(5,2) DEFAULT NULL COMMENT '수수료3',
    `fee_10` DECIMAL(5,2) DEFAULT NULL COMMENT '(특가)수수료',
    `fee_11` DECIMAL(5,2) DEFAULT NULL COMMENT '(용품)수수료',
    `fee_12` DECIMAL(5,2) DEFAULT NULL COMMENT '(특판온라인)수수료',
    `fee_10_info` DECIMAL(5,2) DEFAULT NULL COMMENT '특가기준(%)',
    `id` VARCHAR(30) DEFAULT NULL COMMENT '작성자',
    `bigo` VARCHAR(255) DEFAULT NULL COMMENT '비고',
    `rt` DATETIME DEFAULT NULL COMMENT '등록일자',
    `ut` DATETIME DEFAULT NULL COMMENT '수정일자',
    PRIMARY KEY (`idx`)
) ENGINE=INNODB DEFAULT CHARSET=utf8

-- 상품반품이동
CREATE TABLE `storage_return` (
    `sgr_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify (반품코드)',
    `storage_cd` varchar(30) NOT NULL COMMENT '창고코드 - storage : storage_cd',
    `target_cd` varchar(30) NOT NULL COMMENT '이동처 (공급업체 or 창고)',
    `target_type` char(1) NOT NULL DEFAULT 'C' COMMENT '이동처구분 - 공급업체(C) / 창고(S)',
    `sgr_date` char(10) NOT NULL COMMENT '반품일자',
    `sgr_type` char(1) NOT NULL COMMENT '반품구분 - 일반(G) / 일괄(B)',
    `sgr_state` varchar(30) NOT NULL COMMENT '반품상태 - 접수(10) / 완료(30)',
    `comment` varchar(255) DEFAULT NULL COMMENT '메모',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`sgr_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 상품반품이동 상품리스트
CREATE TABLE `storage_return_product` (
    `sgr_prd_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify (반품일련코드)',
    `sgr_cd` int(11) NOT NULL COMMENT '반품코드',
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `price` int(11) NOT NULL COMMENT '판매가',
    `return_price` int(11) NOT NULL COMMENT '반품단가',
    `return_qty` int(11) NOT NULL COMMENT '반품수량',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`sgr_prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 창고반품
CREATE TABLE `store_return` (
    `sr_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify (반품코드)',
    `storage_cd` varchar(30) NOT NULL COMMENT '창고코드 - storage : storage_cd',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드 - store : store_cd',
    `sr_date` char(10) NOT NULL COMMENT '반품일자',
    `sr_kind` varchar(30) NOT NULL COMMENT '반품종류 - 관리자(S) / 일반(G) / 일괄(B)',
    `sr_state` varchar(30) NOT NULL COMMENT '반품상태 - code : SR_CODE',
    `sr_reason` varchar(30) NOT NULL COMMENT '반품사유 - code : SR_REASON',
    `comment` varchar(255) DEFAULT NULL COMMENT '메모',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`sr_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 창고반품 상품리스트
CREATE TABLE `store_return_product` (
    `sr_prd_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify (반품일련코드)',
    `sr_cd` int(11) NOT NULL COMMENT '반품코드',
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `price` int(11) NOT NULL COMMENT '판매가',
    `return_price` int(11) NOT NULL COMMENT '반품단가',
    `return_qty` int(11) NOT NULL COMMENT '반품수량',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`sr_prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 실사
CREATE TABLE `stock_check` (
    `sc_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify (실사코드)',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드 - store : store_cd',
    `md_id` varchar(30) DEFAULT NULL COMMENT '담당자',
    `sc_date` char(10) NOT NULL COMMENT '실사일자',
    `sc_type` char(1) NOT NULL COMMENT '실사구분 - 일반(G)/일괄(B)',
    `sc_state` char(1) DEFAULT 'N' COMMENT 'LOSS처리상태 - Y / N',
    `comment` varchar(255) DEFAULT NULL COMMENT '메모',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`sc_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 실사 상품리스트
CREATE TABLE `stock_check_product` (
    `sc_prd_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify (실사상품코드)',
    `sc_cd` int(11) NOT NULL COMMENT '실사코드',
    `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
    `price` int(11) NOT NULL COMMENT '판매가',
    `qty` int(11) NOT NULL COMMENT '실사수량',
    `store_qty` int(11) NOT NULL COMMENT '매장수량',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`sc_prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장마감
CREATE TABLE `store_account_closed` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '정산번호',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '매장코드',
    `sday` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '마감기준시작일',
    `eday` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '마감기준종료일',
    `sale_amt` INT(11) DEFAULT NULL COMMENT '판매금액',
    `clm_amt` INT(11) DEFAULT NULL COMMENT '클레임금액',
    `sale_fee` INT(11) DEFAULT '0' COMMENT '판매 수수료',
    `dc_amt` INT(11) DEFAULT '0' COMMENT '할인 금액',
    `coupon_amt` INT(11) DEFAULT NULL COMMENT '쿠폰금액',
    `dlv_amt` INT(11) DEFAULT NULL COMMENT '배송비',
    `allot_amt` INT(11) DEFAULT NULL COMMENT '쿠폰금액(본사부담)',
    `etc_amt` INT(11) DEFAULT NULL COMMENT '기타정산액',
    `sale_net_taxation_amt` INT(11) DEFAULT '0' COMMENT '과세',
    `sale_net_taxfree_amt` INT(11) DEFAULT '0' COMMENT '비과세',
    `sale_net_amt` INT(11) DEFAULT NULL COMMENT '매출금액',
    `tax_amt` INT(11) DEFAULT '0' COMMENT '부과세',
    `fee` INT(11) DEFAULT NULL COMMENT '수수료',
    `fee_dc_amt` INT(11) DEFAULT '0' COMMENT '수수료할인',
    `fee_net` INT(11) DEFAULT '0' COMMENT '수수료총계',
    `acc_amt` INT(11) DEFAULT NULL COMMENT '정산금액',
    `closed_yn` CHAR(1) DEFAULT 'N' COMMENT '마감여부',
    `closed_date` DATETIME DEFAULT NULL COMMENT '마감일시',
    `pay_day` VARCHAR(8) DEFAULT NULL COMMENT '지급일',
    `tax_no` DOUBLE DEFAULT NULL COMMENT '세금계산서번호',
    `admin_id` VARCHAR(50) DEFAULT NULL COMMENT '관리자아이디',
    `admin_nm` VARCHAR(50) DEFAULT NULL COMMENT '관리자명',
    `reg_date` DATETIME DEFAULT NULL COMMENT '등록일시',
    `upd_date` DATETIME DEFAULT NULL COMMENT '수정일시',
    PRIMARY KEY (`idx`),
    UNIQUE KEY `store_cd` (`store_cd`,`sday`,`eday`),
    KEY `sdate` (`sday`,`eday`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장 마감'


CREATE TABLE `store_account_closed_list` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `acc_idx` INT(11) NOT NULL DEFAULT '0' COMMENT '정산번호',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '매장코드',
    `sday` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '마감기준시작일',
    `eday` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '마감기준종료일',
    `type` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '판매 : 30, 교환 : 60, 환불 : 61, 판매 및 교환 : 90, 판매 및 환불 : 91, 기타 : 00',
    `ord_opt_no` INT(11) NOT NULL DEFAULT '0' COMMENT '주문일련번호',
    `state_date` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '마감대상일자 - 출고완료일,클레임완료일,기타정산처리일등',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `sale_amt` INT(11) DEFAULT NULL COMMENT '판매',
    `clm_amt` INT(11) DEFAULT NULL COMMENT '클레임',
    `sale_fee` INT(11) DEFAULT '0' COMMENT '판매 수수료',
    `dc_amt` INT(11) DEFAULT '0' COMMENT '할인 금액',
    `sale_clm_amt` INT(11) DEFAULT NULL COMMENT '판매 + 클레임',
    `coupon_amt` INT(11) DEFAULT NULL COMMENT '쿠폰금액',
    `dlv_amt` INT(11) DEFAULT NULL COMMENT '배송비',
    `etc_amt` INT(11) DEFAULT NULL COMMENT '기타정산액',
    `sale_net_taxation_amt` INT(11) DEFAULT '0' COMMENT '과세',
    `sale_net_taxfree_amt` INT(11) DEFAULT '0' COMMENT '비과세',
    `sale_net_amt` INT(11) DEFAULT NULL COMMENT '매출액',
    `tax_amt` INT(11) DEFAULT '0' COMMENT '부가세',
    `fee_ratio` FLOAT DEFAULT NULL COMMENT '수수료율(%)',
    `fee` INT(11) DEFAULT NULL COMMENT '수수료',
    `fee_dc_amt` INT(11) DEFAULT '0' COMMENT '세금할인액',
    `allot_amt` INT(11) DEFAULT NULL COMMENT '부담액',
    `fee_net` INT(11) DEFAULT NULL COMMENT '수수료',
    `acc_amt` INT(11) DEFAULT NULL COMMENT '정산금액',
    `bigo` VARCHAR(255) DEFAULT NULL COMMENT '비고',
    PRIMARY KEY (`idx`),
    KEY `idx_accidx` (`acc_idx`),
    KEY `store_cd` (`store_cd`,`sday`,`eday`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장 마감내역';

CREATE TABLE `store_account_etc` (
    `no` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `ymonth` VARCHAR(6) DEFAULT NULL COMMENT '정산연월',
    `etc_day` VARCHAR(8) DEFAULT NULL COMMENT '기타정산일자',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '매장코드',
    `ord_no` VARCHAR(20) DEFAULT NULL COMMENT '주문번호',
    `ord_opt_no` INT(11) DEFAULT NULL COMMENT '주문일련번호',
    `etc_amt` INT(11) DEFAULT NULL COMMENT '기타정산액',
    `etc_memo` MEDIUMTEXT COMMENT '기타정산메모',
    `admin_id` VARCHAR(20) DEFAULT NULL COMMENT '관리자아이디',
    `admin_nm` VARCHAR(20) DEFAULT NULL COMMENT '관리자명',
    `regi_date` DATETIME DEFAULT NULL COMMENT '등록일시',
    PRIMARY KEY (`no`),
    KEY `idx_etc_day` (`etc_day`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장 기타정산액';

-- 매장기타재반자료
CREATE TABLE `store_account_extra` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '매장코드',
    `ymonth` VARCHAR(6) NOT NULL COMMENT '정산연월',
    `rt` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
    `type` VARCHAR(10) NOT NULL DEFAULT '' COMMENT 'code - G_ACC_EXTRA_TYPE',
    `extra_amt` INT(11) DEFAULT NULL COMMENT '기타재반자료액',
    PRIMARY KEY (`idx`),
    KEY `store_cd` (`store_cd`, `ymonth`, `type`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장기타재반자료'

-- 월별할인유형적용관리 - 판매유형별
CREATE TABLE `sale_type_apply` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `apply_date` char(6) NOT NULL COMMENT '적용년월 (202208)',
    `sale_type_cd` int(11) NOT NULL COMMENT '판매유형코드 - sale_type : idx',
    `apply_yn` char(1) NOT NULL DEFAULT 'N' COMMENT '적용여부',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='월별할인유형적용관리-판매유형별'

-- 월별할인유형적용관리 - 매장별
CREATE TABLE `sale_type_apply_store` (
    `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
    `apply_date` char(6) NOT NULL COMMENT '적용년월 (202208)',
    `apply_rate` int(11) NOT NULL COMMENT '적용요율',
    `comment` varchar(255) DEFAULT NULL COMMENT '메모',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
    PRIMARY KEY (`idx`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='월별할인유형적용관리-매장별'

-- 매장공지사항
CREATE TABLE `notice_store` (
    `ns_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT '공지번호',
    `subject` varchar(255) DEFAULT NULL COMMENT '공지 제목',
    `content` mediumtext DEFAULT NULL COMMENT '공지 내용',
    `admin_id` varchar(30) DEFAULT NULL COMMENT '작성자ID',
    `admin_nm` varchar(30) DEFAULT NULL COMMENT '작성자명',
    `admin_email` varchar(50) DEFAULT NULL COMMENT '작성자이메일',
    `cnt` int(11) DEFAULT NULL COMMENT '조회수',
    `all_store_yn` char(1) DEFAULT 'N' COMMENT '전체공지여부 - Y / N',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    PRIMARY KEY (`ns_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장공지사항 - 매장별
CREATE TABLE `notice_store_detail` (
    `ns_cd` int(11) NOT NULL COMMENT '공지번호 - notice_store : ns_cd',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
    `check_yn` char(1) DEFAULT 'N' COMMENT '확인여부',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    PRIMARY KEY (`ns_cd`, `store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 알림
CREATE TABLE `msg_store` (
    `msg_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT '알림번호',
    `sender_type` char(1) NOT NULL COMMENT '발신처 타입 (매장 - S / 본사 - H)',
    `sender_cd` varchar(30) NOT NULL COMMENT '발신처코드',
    `reservation_yn` char(1) NOT NULL COMMENT '예약발송여부',
    `reservation_date` varchar(20) DEFAULT NULL COMMENT '예약발송일 (0000-00-00 00:00:00)',
    `content` mediumtext DEFAULT NULL COMMENT '알림 내용',
    `rt` datetime DEFAULT NULL COMMENT '등록일자 (발신일자)',
    PRIMARY KEY (`msg_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 알림 - 수신처별
CREATE TABLE `msg_store_detail` (
    `msg_cd` int(11) NOT NULL COMMENT '알림번호 - msg_store : msg_cd',
    `receiver_type` char(1) NOT NULL COMMENT '수신처 타입 (매장 - S / 본사 - H)',
    `receiver_cd` varchar(30) NOT NULL COMMENT '수신처코드',
    `check_yn` char(1) NOT NULL COMMENT '알림확인여부',
    `check_date` datetime DEFAULT NULL COMMENT '알림확인일시',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    PRIMARY KEY (`msg_cd`, `receiver_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장그룹
CREATE TABLE `msg_group` (
    `group_cd` int(11) NOT NULL AUTO_INCREMENT COMMENT '그룹번호',
    `group_nm` varchar(100) NOT NULL COMMENT '그룹명',
    `account_cd` varchar(30) NOT NULL COMMENT '그룹추가한 계정(매장)코드',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    `ut` datetime DEFAULT NULL COMMENT '수정일자',
    PRIMARY KEY (`group_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장그룹 내 매장정보
CREATE TABLE `msg_group_store` (
    `group_cd` int(11) NOT NULL COMMENT '그룹번호 - store_group',
    `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
    `rt` datetime DEFAULT NULL COMMENT '등록일자',
    PRIMARY KEY (`group_cd`, `store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 원부자재 출고
CREATE TABLE `sproduct_stock_release` (
    `idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
    `type` VARCHAR(50) DEFAULT NULL COMMENT '분류 - code : REL_TYPE (요청분/일반 : R/G)',
    `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
    `price` int(11) NOT NULL COMMENT '판매가',
    `wonga` int(11) NOT NULL COMMENT '원가',
    `qty` INT(11) DEFAULT NULL COMMENT '수량',
    `store_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '수령매장코드',
    `storage_cd` VARCHAR(30) NOT NULL DEFAULT '0' COMMENT '창고코드',
    `state` INT(11) NOT NULL DEFAULT '0' COMMENT '상태(요청/접수/출고/입고(매장)/거부:10/20/30/40/-10)',
    `exp_dlv_day` VARCHAR(8) DEFAULT NULL COMMENT '출고예정일자',
    `rel_order` VARCHAR(30) DEFAULT NULL COMMENT '출고차수 - 출고예정일자 + code : REL_ORDER (01 - 25)',
    `req_comment` VARCHAR(255) DEFAULT NULL COMMENT '요청메모',
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

--
-- 기존 테이블 컬럼 추가 시작
--

-- 브랜드에 '브랜드 단축코드' 추가
ALTER TABLE `bizest_smart`.`brand` ADD COLUMN `br_cd` VARCHAR(3) NULL COMMENT '단축코드' AFTER `brand_nm_eng`;

-- 회원에 오프라인 회원 일괄 등록 필드 추가
ALTER TABLE `bizest_smart`.`member` ADD COLUMN `type` CHAR(1) NOT NULL DEFAULT 'N' COMMENT '회원가입 종류 - N : 일반, B : 일괄(XMD)' AFTER `prom_code`;
ALTER TABLE `bizest_smart`.`member` ADD COLUMN `store_nm` VARCHAR(100) DEFAULT NULL COMMENT '회원가입 매장명' AFTER `type`;
ALTER TABLE `bizest_smart`.`member` ADD COLUMN `store_cd` VARCHAR(30) DEFALUT NULL COMMENT '회원가입 매장코드' AFTER `store_nm`;

-- 입고
ALTER TABLE `bizest_smart`.`stock_product` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `brand`;

-- 주문에 '상품코드' 추가
ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `out_ord_no`;
-- ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `sale_kind` VARCHAR(30) NULL COMMENT '판매유형' AFTER `store_cd`;
-- ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `pr_code` VARCHAR(30) NULL COMMENT '행사구분' AFTER `sale_kind`;
-- 위 2개 항목 order_msg => order_opt 변경 (2022-08-23)

ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `sale_kind` VARCHAR(30) NULL COMMENT '판매유형' AFTER `store_cd`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `pr_code` VARCHAR(30) NULL COMMENT '행사구분' AFTER `sale_kind`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `prd_cd`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `out_ord_opt_no`;

ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `tax_fee`;
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `prd_cd`;

ALTER TABLE `bizest_smart`.`order_opt` ADD INDEX `idx_store_cd_orddate` (`store_cd`, `ord_date`);
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD INDEX `idx_ord_state_date` (ord_state_date,ord_state,store_cd);


--
-- 기존 테이블 컬럼 추가 종료
--

--
-- 테이블 데이터 추가 시작
--

insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values
('P_STOCK_TYPE','10','매입','','','','Y','0','ceduce','본사_김용남',now(),now()),
('P_STOCK_TYPE','20','주문','','','','Y','0','ceduce','본사_김용남',now(),now()),
('P_STOCK_TYPE','11','반품','','','','Y','0','ceduce','본사_김용남',now(),now()),
('P_STOCK_TYPE','30','재고조정','','','','Y','0','ceduce','본사_김용남',now(),now());

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
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','02','쿠폰판매(10%)','','','','Y','2','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','03','쿠폰판매(15%)','','','','Y','3','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','04','쿠폰판매(20%)','','','','Y','4','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','05','5% 할인','','','','Y','5','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','06','10%할인','','','','Y','6','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','07','20%할인','','','','Y','7','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','08','30%할인','','','','Y','8','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','09','브랜드데이10%','','','','Y','9','ceduce','본사_김용남',now(),now());
-- insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','10','임직원20%할인(당월 매출 10%)','','','','Y','10','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','00','일반판매','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','09','5% 할인헹시','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','10','10% 할인','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','12','20% 할인','','','','Y','4','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','13','30% 할인','','','','Y','5','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','50','50% 할인','','','','Y','6','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','54','사은품 증정','','','','Y','7','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','81','온라인판매','','','','Y','8','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','C1','10%가격행사(칼라별)','','','','Y','9','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','C2','20%가격행사(칼라별)','','','','Y','10','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','CB','25%가격행사(칼라별)','','','','Y','11','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','C3','30%가격행사(칼라별)','','','','Y','12','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','17','브랜드데이(10%)','','','','Y','13','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','18','한바그프로모션(10%)','','','','Y','14','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SALE_KIND','19','프리머스프로모션(10%)','','','','Y','15','ceduce','본사_김용남',now(),now());

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

-- code_kind 데이터 추가 상품코드 - 아이템 : PRD_CD_ITEM
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','[매장관리]상품코드 - 아이템','RPD_CD_ITEM','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 상품코드 - 아이템 : PRD_CD_ITEM
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','AC','Acc','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','BA','Bag','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','BE','Belt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','BP','Backpack','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','KK','Kanken','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','BT','Boots','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','CA','Cap','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','CB','COMPUTER BAG','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','CD','Cardigan','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','CH','Chair','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','DP','Daypack','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','DW','DOWN','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','FD','food','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','FE','Fleece','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','GL','Glove','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','HD','Hood Zipup','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','HO','Hoodie','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','JK','Jacket','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','JP','Jumper','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','KJ','Knit Jacket','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','KN','Knit','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','LC','Laptop Case','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','LS','Long Shirt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','MB','MINI BAG','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','MS','MESSENGER BAG','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','OP','One Piece','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','PA','Parka','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','PC','Pouch','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','PD','Padding','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','PO','Poncho','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','PT','Pants','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SB','Sleeping','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SC','Scarf','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SE','SPECIAL EDITION','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SG','Shoulder Bag','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SH','Shirt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SK','Skirt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SL','Long Sleeve','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SO','Shoes','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SR','Shorts','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SS','Short Shirt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SU','Sunglasses','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SW','Sweater','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','TB','Tote Bag','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','TE','Tents','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','TO','Top','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','TR','Trousers','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','BB','BALLISTIC BAG','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','TS','T-SHIRT','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','VB','VINYL BAG','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','VE','Vest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','HL','Short Sleeve','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','ZP','Zip up','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','WC','Watch','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','ST','Stick','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','BL','Bottle','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','CK','Cooker','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','FN','Furniture','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SV','Stove','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','LT','Lantern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','TL','Tool','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SQ','Socks','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','FL','Fuel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_ITEM','SP','Spare Parts','','','','Y','0','ceduce','본사_김용남',now(),now());

-- code 데이터 추가 행사코드 : PR_CODE
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PR_CODE','JS','정상','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PR_CODE','GL','행사','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PR_CODE','J1','균일','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PR_CODE','J2','용품','','','','Y','1','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 행사코드 : PR_CODE
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PR_CODE','[매장관리]행사코드','pr_code','Y',NULL,'0','','본사_김용남',now(),now());

-- code_kind 데이터 추가 상품코드 - 아이템 : PRD_CD_COLOR
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','[매장관리]상품코드 - 컬러','RPD_CD_COLOR','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 상품코드 - 아이템 : PRD_CD_COLOR
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','00','None','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0A','Dessert','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0B','Baby Lilac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0D','Dark Grey/Storm','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0G','Dark Garnet/Plum','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0M','Multi Block','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0R','Barn Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1A','Dark Grey Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1B','Black/Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1C','Light Stone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1D','Deep Bordeaux','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1E','Deep Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1F','Cloud','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1G','Grey/Silver','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1H','Petrol','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1I','Comme Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1J','Roiboos','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1K','Multi','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1L','Grey Stripe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1M','MINT/BLACK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1N','Grey Check','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1O','Mauve Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1P','Pool Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1Q','Powder Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1R','Night Bule/Dk Turquoise','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1S','Granite Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1T','Bleu/Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1U','Gris Fonce/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1V','Violet/Smar Ge Check','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1W','Carmin/Crimson','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1X','Dusk Blue Print','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1Y','Navy/Grey/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','1Z','Rock Black Aop','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2A','Asphalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2B','Blueberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2C','Clay/Washed Clay','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2D','Dp Brdx Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2E','Asphalt/Anthracite','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2F','Off White／Kelly Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2G','Geometric','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2H','Summer Landscape','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2I','Spring Landscape','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2J','Asphalt/Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2K','Black/Royal Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2L','Lilac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2M','Midnight Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2N','Natural Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2O','Dark Grey/Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2P','Purple／Warm Gray','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2Q','Asphalt/Dark Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2R','Rust','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2S','Sand/Tarmac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2T','Tallow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2U','Asphalt/Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2V','Lagoon/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2W','Peacock Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2X','Asphalt/Light Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2Y','Asphalt/Orink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','2Z','Asphalt/Ocean','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3A','Shark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3B','Sapphire Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3C','Creek Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3D','Dp Brdx/Ecru','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3E','Rowan Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3F','Deep Red/Folk Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3G','Grey Move','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3H','Light Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3I','Ochre/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3J','Dark Navy/Lava','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3K','Green/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3L','Lava','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3M','Marine Blue Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3N','Night Sky','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3O','Tarmac/Dark Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3P','Bright Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3Q','Birch Forest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3R','Rhubarb Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3S','Soft Peach','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3T','Blue Fable','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3U','Green Fable','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3V','Cherry Violet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3W','Mineral Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3X','Golden Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3Y','Clay Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','3Z','Raspberry Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4A','Pastel Lavender','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4B','Matt Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4C','Dark Cobblestone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4D','Dp Brdx/Indian Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4E','Driftwood','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4F','Foam','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4G','Spring Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4H','Sand Stone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4I','Shark Grey/Super Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4J','Ochre/Golden Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4K','Savanna/Light Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4L','Lily','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4M','Marine Blue/Ecue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4N','Dandelion','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4O','Orange Waves','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4P','Palm Leaves','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4Q','Ochre/Super Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4R','Royal Blue/Pinstripe Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4S','Savanna','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4T','Tarmac/Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4U','UN Blue/Stone Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4V','Laurel Green/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4W','Super Grey/Chess Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4X','River Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4Y','Chest Nut/Acorn','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','4Z','Dark Navy/Light Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5A','Olive/Deep Forest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5B','Burnt Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5C','Copper Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5D','Dp Brdx/Mid Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5E','Rowan Red/Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5F','Free Frog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5G','Olive Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5H','Uncle Blue/Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5I','Mineral Blue/Clay Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5J','Mocca/Asphalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5K','Asphalt/Asphalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5L','Frost Green/Light Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5M','Midnight Grey/Dp Brdx','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5N','Salmon Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5O','Black/Asphalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5P','Pink Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5Q','Mocca/Tan','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5R','Red Oak','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5S','Seashell Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5T','Navy/Asphalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5U','Black/Cognac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5V','Navy/Cognac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5W','Black/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5X','Ochre/Chess Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5Y','Clay','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','5Z','Flamingo Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6A','Pastel Lavender/Cool White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6B','Black/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6C','Cognac_Gemse','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6D','Desert Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6E','Autumn Leaf/Stone Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6F','Dark Forest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6G','Cloudburst Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6H','Mint Green/Cool White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6J','Spruce Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6K','Spruce Green/Clay','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6L','Laurel Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6M','Maroon Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6N','Peach Pink/Lagoon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6O','Dark Garnet/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6P','Pink Rose','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6Q','Mustard Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6R','Deep Red/Random Blocked','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6S','Mushroom','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6T','Dark Olive Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6U','Ocean Surface','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6V','Ocean Deep','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6W','Deep Turquoise','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6X','Corn','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6Y','Cabin Red/Rowan Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','6Z','Ember Orange/Super Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7A','Mocca/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7B','Night Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7C','Deep Forest/Acorn','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7D','Dark Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7E','True Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7F','Fern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7G','Military Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7H','Dark Sand/Stone Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7J','Laurel Green/Green Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7L','Guacamole','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7M','Maroon Red/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7N','NV/FE/PP','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7P','Pitaya Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7R','Deep Blue/Rainbow Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7S','Dark Sea','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7T','Timber Brown/Chestnut','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8A','Air Blue/Striped','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8B','Black/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8C','Chestnut/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8D','Dotted Checks','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8E','Bogwood Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8F','FR Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8G','Shadow Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8K','Buckwheat Brown/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8L','Stone Grey/Lava','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8M','Marble Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8N','Navy/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8P','Pink/Air Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8R','Purple/Rainbow Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8S','Storm','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8U','New Moon Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8W','Buckwheat Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8Y','Black/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9A','Deep Forest/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9B','Black Multicam','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9C','Charcoal/Washed Charcoal','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9D','Duckcamo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9F','Frost Green/Chess Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9G','Glacier Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9M','Midnight Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9N','Dark Navy/Uncle Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9P','Pale Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9R','Real Tree','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9S','Black/Striped','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9T','Arctic Green/Storm','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A0','Antra Mel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A1','Amber','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A2','ACID black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A3','ACID light blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A4','Camel Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A5','Amazonia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A6','Azur Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A7','Granite Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A8','Amry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','A9','Lagoon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AA','Anthracite','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AB','Atlantic Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AC','ANCHOR','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AD','Autumn Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AE','AURORA RED','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AF','Aqua/Fuchsia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AG','ARMY/GRIS CHINE CLAIR','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AH','Alpine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AI','Aubergine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AJ','Navy Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AK','Ash Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AL','Alpin','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AM','PALM','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AN','Auburn','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AO','Avocado','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AP','Apricot','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AQ','AQUA','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AR','Army Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AS','Asche_Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AT','Autumn Leaf','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AU','AURORA RED','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AW','Acorn','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AY','Aubergine/Cranberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AZ','AZURIN','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B0','Blanc/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B1','Beige Foncce/Dark Beige','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B2','Black Studs','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B3','Black RG ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B4','Beige/Beige','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B5','BLUE IRIS','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B6','BRACKET BLACK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B7','T-BLUE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B8','Bleu Curacao/Blue Curaco','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','B9','Bleu Grise/Blue Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BA','BLANC','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BB','Black Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BC','BLANC/BATZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BD','BLONDE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BE','BLANC/ETOILE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BF','Bleu Flo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BG','Beige','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BH','BLANC/RICH NAVY','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BI','Brick','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BJ','Burgundy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BK','Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BL','Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BM','Bright Lemon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BN','Blue Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BO','Burnt Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BP','Black Plum','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BQ','Bay Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BR','BLANC/BRAISE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BS','Bison','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BT','BATZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BU','BLUE DENIM','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BV','Bay Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BW','Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BX','Black/Ox Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BY','Black/Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','BZ','Bordeaux','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C0','Black/Graphite','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C1','Chataigne/Sweet Chestnut','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C2','Green Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C3','Charcoal Chambray','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C4','Choco Turquoise','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C5','CAFE ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C6','CEMENTO ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C7','CARBON GREY','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C8','Clay Grey/Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','C9','Coral','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CA','CARAMEL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CB','CLOUD BLUE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CC','Chocolat/Chocolat','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CD','Coral Red/Stone Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CE','Crimson','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CF','CAMOUFLAGE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CG','Charcoal','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CH','Cheetah','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CI','Cerise','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CJ','Coral Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CK','CHEVRON BLACK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CL','Cranberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CM','Camel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CN','Cayenne Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CO','Cobalt/RedOrange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CP','Cherry Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CQ','Cherry Red Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CR','Cream','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CS','SAND/COGNAC','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CT','Marone_Chestnut','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CU','Cherry Red/Marine Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CV','Caribbean Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CW','Schwarz_Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CX','Cobalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CY','Cherry Red/Ecru','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','CZ','Cobalt/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D0','Dark Leo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D1','Dove','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D2','Denim Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D3','Denim 02','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D4','Denim','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D5','Dune Metric Camo/Red Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D6','Dark Grey/Red Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D7','Denim/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D8','Deux','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','D9','Deep Cobalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DA','Dark Garnet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DB','Dark Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DC','Dragee','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DD','Deep Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DE','Dark Grey/Red Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DF','Dune/Bayleaf','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DG','Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DH','Dark Havana+ Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DI','Dark Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DJ','Deep Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DK','Dark Navy/Off White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DL','Green/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DM','Dark Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DN','Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DO','Dark Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DP','Dark Pine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DQ','Dark Grass','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DR','Dark Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DS','Dots','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DT','Desert','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DU','Deep Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DV','Dark Violet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DW','Dove Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DX','Dark Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DY','Dark Grey/Pinkberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','DZ','Denim Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E1','Espresso','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E2','Earth','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E3','Eggshell','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E6','Spicy Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E9','Mesa Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EA','Ecru/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EB','Ebene','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EC','Ecru','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ED','Erde_Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EF','Coffee','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EG','Eggplant','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EH','Heather','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EI','Eibsee','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EL','ELK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EM','Emerald','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EN','NAVYMEL/ROYALBLUE MEL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EO','Coquille/Egg Shell','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EP','Patrol','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ER','Acorn/Ox Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ES','Estate Blue/Salvia Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ET','Estate Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EU','Eucalyptus','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EY','Lemon Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F0','Fleurs Retro/Retro Flowers','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F1','Floral','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F2','Framboise/Raspberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F3','Foliage Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F4','Fog/Chalk White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F5','Green/Folk Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F6','Deep Forest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F7','Fog/Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F8','Stone Red/ Off White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','F9','Flame Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FA','Foliage','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FB','Forest Green/Terra Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FC','Fuchsia/Insignia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FD','Forest Shade','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FE','Frost Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FF','Smaragd green/Offwhite','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FG','Forest Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FH','Fushia/Fuschia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FI','Fuchsia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FJ','Flame Orange/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FK','FG/OR/RY','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FL','Green Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FM','Flame Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FN','Forest Green/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FO','Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FP','Frost Green/Peach Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FQ','FE/PP/NV','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FR','FirGreen/GoldenRod','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FS','Sea Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FT','Ficelle/Twine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FU','Fuxia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FV','Laurel Green/Deep Forest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FW','Campfire Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FX','Forest Green/Ox Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FY','Fluro Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','FZ','Fog/Mountain Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G0','GREY MEL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G1','Gris/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G2','Gun Metal','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G3','GunMetal','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G4','Black Print','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G5','Gris Moyen/Medium Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G6','Grey/Stripe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G7','Grey/Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G8','Blue/Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','G9','Burgundy/Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GA','Graphite','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GB','Glacier Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GC','Gletscher','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GD','Gold','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GE','Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GF','GT Fuzzy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GG','Grass Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GH','Bottle Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GI','Gold Foil/Blanc','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GJ','Green/Off White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GK','Grey Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GL','Gold Foil Linen/Blanc','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GM','Gemse_Tan','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GN','GRIS CHINE CLAIR/NAVIRE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GO','Black/Goose Grey Reno Plaid','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GP','Gris Perle/Pearl Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GQ','GRIS CHINE CLAIR/QUARTZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GR','Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GS','GRIS CHINE CLAIR','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GT','Garnet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GU','Graphite/UN Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GV','Grey Silver','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GW','Guava Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GX','Grey/Navy Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GY','Gris Clair/Light Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','GZ','Grey/Lemon Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','H3','Chalk White/Cabin Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','H6','Light Olive/Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','H8','Chestnut/Timber Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HA','Horizon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HB','BURNT HAVANA','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HC','Chambray','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HD','Hunting Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HE','CHEVRON BUTTERSCOTCH','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HG','Birch Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HH','Chestnut','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HK','Hickory Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HL','Hunting Green/Midnight Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HM','Hunting Green/Maroon Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HN','Nuss_Hazelnut','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HO','Honig','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HP','Hot Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HQ','Asphalt/Dark Garnet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HR','High Risk Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HS','Hibiscus','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HT','HOUND TOOTH','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HV','Havana Glitter','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HW','Chalk White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HX','Asphalt/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HZ','Horizon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','I1','Ivory','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','I6','Lichen Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','I9','Arctic Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IB','Ice Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IC','INSIDE CAMO','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ID','Dark Grey/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IG','Indigo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IK','Ink Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IM','Ink Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IN','Ink Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IO','Dark Olive/Taupe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IP','Ice Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IR','Rainbow Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IS','Prism Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IT','Dark Olive/Sand Stone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IV','Indian Yellow/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IY','Indian Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','J1','Jamaican','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JA','Faded Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JB','JEANS BLUE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JC','Cabin Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JD','Storm/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JG','Stone Grey/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JJ','Jaune','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JM','Mountain Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JN','Storm/Night Sky','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JP','Alpine Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JR','Red/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JS','Stainless Steel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JY','Dark Grey/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','K1','Kaki/Khaki','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','K2','KHAKI ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','K3','Black/Ox Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','K4','Black/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','K6','Navy/Long Stripes','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KA','KANNEL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KB','Blackberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KC','Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KD','Black Dust','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KG','Dark Grey/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KH','Khaki','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KJ','Dark Garnet/Night Sky','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KK','Black Camo/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KL','Dark Lava','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KM','Black Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KN','Khaki/Night Sky','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KO','Hokkaido Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KP','PUPLE CHECK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KR','Ox Red/Goose Eye','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KS','Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KY','Red Oak/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L0','Lime Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L1','Soft Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L2','Light Army','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L3','Lake Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L4','Liver','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L5','Lie De Vin/Wine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L6','Rose/Bailerina Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L7','Light Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L8','Lake Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','L9','Light Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LA','Lime Punch','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LB','Light Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LC','Lichen','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LD','Loden','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LE','Light Beige Bunnies','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LF','Light Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LG','Light Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LH','Larche_Light Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LI','Lime','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LJ','Light Grey Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LK','Light Khaki','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LL','Lilla','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LM','Limone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LN','Leather Natural','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LO','Leopard','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LP','Light Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LQ','Leather Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LR','Leather Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LS','Lumiere Rose','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LT','Light Turquoise','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LU','Black/Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LV','Lavender','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LW','Limestone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LX','Leather Cognac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LY','Leaf Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','LZ','Leaves','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M0','Dark Blue Check','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M1','Marron/Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M2','MORADO ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M3','MARINO ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M4','Multi Army','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M5','Medieval Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M6','Moss Green/Red Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M7','Mint','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M8','Metalic Gold','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','M9','Metalic Bronze','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MA','Mango','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MB','MELANG BLUE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MC','Mustard','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MD','Mint Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ME','M/Linen','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MF','Multistripe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MG','Moss Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MH','Blue Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MI','Midnight','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MJ','Mountain Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MK','Multi Colour Check','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ML','MISTY BLUE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MM','Menthe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MN','M/Noir','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MO','Moss','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MP','Malabar/Bubblegum Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MQ','Marine_Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MR','Marron','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MS','Mastic','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MT','Mattone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MU','Mud','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MV','Black Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MW','Midnight Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MX','MULTI COLOUR CHECK 2','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MY','Mud/Putty','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','MZ','Mint Green/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N0','Navy/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N1','Neon Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N2','Natural Canvas','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N3','NEGRO ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N4','Night Blue/Ocean','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N5','New Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N6','Nigth Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N7','New Navy/Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N8','Navy/Grey/Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','N9','New Moss','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NA','NAVIRE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NB','Noir Blanc','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NC','Cognac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ND','NAVIRE/TANDORI','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NE','NAVY/RED','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NF','Navy/Off White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NG','Night','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NH','NAVIRE/SPAHI/SOFT','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NI','NATURAL/RICH NAVY','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NJ','NoilJC/Noir','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NK','Snake Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NL','Navy Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NM','Navy Mel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NN','NATURE/NAVIRE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NO','NeonRed','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NP','NAVIRE/PELOUSE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NQ','NATURE/QUARTZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NR','NATURAL/RED','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NS','NATURE/SAPHIR','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NT','NAVIRE/NATURE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NU','Nero','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NV','Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NW','Navy/Warm Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NX','Navy/Smaragd Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NY','Navy/Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','NZ','NAVIRE/SOFT/BATZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O1','Orcre/Yellow Earth','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O2','Ocre Yellow/Stone Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O5','Military Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O6','Charcoal Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O9','Port/Mesa Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OA','Off White Print','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OB','Ox Red/Brick','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OC','Ochre','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OD','Dark Olive/Dark Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OE','Orange/Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OF','Orange Flame','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OG','Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OH','Ocean Depths','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OI','Leather Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OJ','OR/RY/FG','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OK','Off Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OL','Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OM','Orange/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ON','Ocean Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OP','Orchid','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OQ','Ocean Mist','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OR','Ox Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OS','Oyster','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OT','Ox Red/Putty','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OV','Medium Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OW','Ox Red/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OX','Ox Red/Lava','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OY','Ocre Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P0','Grey Print','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P1','Panther','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P2','Wine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P3','Tile Print','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P4','PRINT WIRE BLACK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P5','Prune/Plum','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P6','Port','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P7','Pant Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P8','Dusty Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','P9','Peach','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PA','Parpas','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PB','Puma','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PC','Port/Ceramic','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PD','Pompien Red/Lead','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PE','PELOUSE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PF','Pflaume','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PG','Reflective Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PH','Persian Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PI','Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PJ','Pine Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PK','Pink Snake','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PL','Port/Red Clay/Ceramic','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PM','PUMPKIN','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PN','Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PO','Pomme','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PP','Peach Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PQ','Plum','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PR','Pink/RoyalBlue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PS','Pistacchio','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PT','Putty','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PU','Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PV','Polvere_Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PW','Powder Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PX','Pastacchio','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PY','Pink Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','PZ','Pink/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Q1','Quarry Metric Camo/Ultramarine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Q6','Dark Garnet/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Q7','Uncle Blue/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QB','Brique','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QC','Chrome','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QD','Deep Forest/Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QG','Concrete Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QL','Dahlia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QM','Meadow Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QN','Dark Navy/Dark Garnet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QP','PP/NV/FE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QQ','Water Colors','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QR','Black/Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QS','Fog/Striped','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QU','QUARTZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QY','Navy/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R0','Bright Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R1','Royal/Royal Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R2','ROUX/Dark Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R3','ROJO ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R4','RUSTY ORANGE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R5','Rouge/Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R6','Raspberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R7','Raspberry/Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R8','Rose Gold','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','R9','Stone Red Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RA','RICH NAVY/NATURE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RB','RICH NAVY/BATZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RC','Red/Cobalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RD','Royal Blue/Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RE','Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RF','Red Riff','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RG','Raisin/Grape','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RH','Royal Lilla','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RI','Rubin_Bright Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RJ','RY/FG/OR','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RK','Retroski Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RL','RICH NAVY','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RM','RICH NAVY/MOKA','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RN','RED/NATURAL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RO','ROUILLE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RP','Blue/Orange Check','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RQ','Cork','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RR','Crimson Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RS','ROSE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RT','Royal Lila/Mint Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RU','Russet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RV','Lava/Dark Lava','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RW','Redwood','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RX','Royal Blue/Ox Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RY','Royal Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','RZ','Red Gold','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S0','Sand Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S1','SEPIA/BROWN','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S2','SEAL BROWN','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S3','Stone Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S4','Sand Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S5','Stone Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S6','SALMON','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S7','Stone Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S8','Stone Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','S9','Sun Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SA','Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SB','Soft Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SC','Steel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SD','Signal Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SE','Sage','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SF','Safran','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SG','Stone Gray','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SH','Greyish Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SI','SOIL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SJ','Salvia Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SK','Sky Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SL','Sand Grey/Light Golden','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SM','Smaragd Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SN','Soft Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SO','Safety Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SP','STRIPE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SQ','BRIQUE MEL/SMA GE MEL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SR','Smaragd','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SS','Black Stripe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ST','Steel Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SU','Blue Petrol','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SV','Sliver','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SW','Strawberry','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SX','Snow Leo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SY','Sangia','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','SZ','Stone Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T1','Tartan Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T2','TURBA ENZ','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T3','Trois','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T4','Turquoise Print','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T5','Steel Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T6','Royal Blue/Flamingo Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T8','Timber Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TA','Tarmac','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TB','Teal Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TC','Bright Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TD','TANDORI','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TE','Terra Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TF','TOFFEE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TG','Teal Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TH','Taupe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TI','Titanium Gray','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TJ','Thunder Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TL','Royal Blue/Goose Eye','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TM','Magenta','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TN','Tangerine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TO','TABACCO','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TP','Taupe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TQ','Tiger','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TR','Torf','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TS','Steel Blue Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TT','Turquoise','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TU','Truffle','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TW','TAN/WHITE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TY','Frost Green/Fog','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','U1','Marine Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','U2','Nude','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','U6','Super Grey/Stone Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','U7','Uncle Blue/Sand Stone','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','U8','Arctic Green/Buckwheat Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','U9','Autumn Leaf/Laurel Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UA','Mud Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UB','Uncle Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UC','Citrus','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UD','Blinded Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UE','Sea Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UG','UltraMarine/GoldenRod','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UH','UN Blue/Warm Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UL','UN Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UM','UMBRA','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UN','Un Blue/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UP','Pastel Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UR','Bluebird','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','US','Dusk Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UT','Acorn/Chestnut','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UU','Summer Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UV','Summer Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UX','BORDEAUX MELANGE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V1','Violine/Deep purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V2','Vert De Gris/Verdegris','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V3','Violet/Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V4','Vivid','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V5','VIOLET MEL/ANTRA MEL','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V6','Vintage Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VA','Vintage Aqua','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VB','Brown Vintage','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VC','Navy/Cherry Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VD','Verde','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VE','Vintage Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VG','NAVY/GREY MELANGE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VI','Violet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VL','Vanilla','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VM','Melon Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VN','Navy/Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VO','Virgin Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VP','Deep Forest/Laurel Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VR','Vintage Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VS','Slate','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VT','Deep Violet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VU','Navy/Ecru','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VV','Vert/Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W0','White Urban','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W1','WRITING BLUE IRIS','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W2','WRITING PORT WINE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W3','WRITING MOONSTRUCK','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W4','Off White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W5','White/Stripe','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W6','White/Green/Gold','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W7','White/Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W8','White/Mint','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','W9','Lie De Vin/Beige','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WA','Chestnut/Acorn','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WB','Black/White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WC','WOODLAND CAMO/NAVY/RED','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WD','White/Dark Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WE','Wild Ginger','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WG','White/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WH','White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WI','Willow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WK','Waikiki beach','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WL','Wine Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WM','Brown Mel','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WN','Brown/Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WO','Vintage Flowers','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WP','Puple Power','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WQ','West Marine','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WR','White Rose','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WS','White Snake','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WV','Warm Yellow/Random Blocked','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WW','WINTER WHITE','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WY','Warm Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XA','Air Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XB','Swedish Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XC','Chestnut/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XD','Dusk','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XG','Golden Poppy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XL','Terracotta Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XM','Melon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XN','Dark Garnet/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XO','Orange Camo','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XP','Plum/Dark Garnet','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XR','Ox Red/Royal Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XS','Bordeaux/Steel Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Y6','Dark Navy/Arctic Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YB','Black/Stone Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YC','Coyote','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YD','Deep Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YE','Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YG','Super Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YK','Navy/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YL','Autumn Leaf/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YM','Ocre Yellow Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YN','Natural Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YO','Green Camo/Laurel Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YP','Royal Purple','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YR','Ruby Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YS','BEACH BOYS','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YT','Dark Navy/Steel Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YW','Maple Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z7','Asphalt/Dusk','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z8','Navy/Light grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z9','Asphalt/Petrol','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZB','Blue Ridge','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZC','Maroon','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZD','Blue Ridge/Random Blocked','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZG','Dark Grey/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZL','Azure Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZM','Mandarine Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZN','Dark Olive/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZO','Green Camo/Deep Forest','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZS','Sage Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EK','Grey/Melange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E0','Light Grey/Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZE','Asphalt/Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZI','Asphalt/Mint','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','H0','Century/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E5','Patina Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0L','Light Oak','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZP','Pink/Long Stripes','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7K','Sky Blue/Light Oak','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UY','Cloud Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IL','Lilac Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0S','Coast Line/Sky','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0C','Poppy Fields/Cotton Sky','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9L','Landsort','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','XE','Bordeaux Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IE','Pomegranate Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JE','Jade Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T7','Dark Navy/Patina Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OU','Indigo Blue/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EX','Patina Green/Dark Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EV','Green/Alpine Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EE','Arctic Green/Patina Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IU','Alpine Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','TV','Patina Green/Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UO','Pomegranate Red/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','OZ','Pomegranate Red/Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UZ','Indigo Blue/Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KZ','Black/Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9U','Indigo Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8T','Sand Stone/Light Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','I5','Patina Green/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O8','Landsort Pink/Landsort Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','VW','Chalk White/Light Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0E','Sage Green/Chalk White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0Y','Sky/Chalk White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0I','Landsort Pink/Chalk White','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E8','Laurel Green/Light Olive','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E4','Jade Green/Patina Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9I','Pomegranate Red/Iron Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KI','Black/Iron Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','UI','Indigo Blue/Iron Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z1','Asphalt/Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z2','Asphalt/Bluegreen','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z3','Asphalt/Light Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9Z','Cappuchino/Asphalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9E','Navy/Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8Q','Black/Light Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Q8','Light Grey/Pink','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z5','Rainbow Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','Z6','Rainbow Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JL','Light Beige','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','T0','Terracotta Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','V0','Dark Olive/Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0F','Flint Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0P','Peach Sand','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZF','Frost Green/Confetti Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0T','Peach Sand/Terracotta Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZZ','Arctic Green/Spicy Orange','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9W','Waterfall Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HF','Ochre/Confetti Pattern','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','E7','Desert Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','ZR','Caper Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0W','Chalk White/Indigo Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JT','Storm/True Red','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','KT','Arctic Green/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IF','Buckwheat Brown/Autumn Leaf','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','0N','Dark Navy/Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','AV','Navy/Autumn Leaf','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9Y','Mais Yellow','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','EW','Aloe Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7Y','Flint Grey/Iron Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','HY','Port/Iron Grey','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QT','Autumn Leaf/Terracotta Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','WT','Chalk White/Buckwheat Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7W','Buckwheat Brown/Laurel Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','7O','Deep Forest/Patina Green','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','8O','Flint Grey/Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','9O','Buckwheat Brown/Light Beige','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O3','Autumn Leaf/Dark Navy','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O4','Mountain Blue/Basalt','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','O7','Mountain Blue/Mountain Blue','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','IA','Buckwheat Brown/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QA','Cork/Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','QE','Forest/Cork','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','JF','Dark Forest/Natural Brown','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','H5','Dark forest/Black','','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_COLOR','YA','Cork/Beige','','','','Y','0','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 상품코드 - 아이템 : PRD_CD_SIZE_MATCH
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','[매장관리]상품코드 - 사이즈 매칭','RPD_CD_SIZE_MATCH','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 상품코드 - 아이템 : PRD_CD_SIZE_MATCH
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','0L','0L','L(한국사이즈XL)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','0M','0M','M(한국사이즈L)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','0S','0S','S(한국사이즈M)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','10','10','10 (한국사이즈 290)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','10.5','10.5','10.5 (한국사이즈 295)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','3.5','3.5','3.5 (한국사이즈 225)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','4','4','4 (한국사이즈 230)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','4.5','4.5','4.5 (한국사이즈 235)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','5','5','5 (한국사이즈 240)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','5.5','5.5','5.5 (한국사이즈 245)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','6','6','6 (한국사이즈 250)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','6.5','6.5','6.5 (한국사이즈 255)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','7','7','7 (한국사이즈 260)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','7.5','7.5','7.5 (한국사이즈 265)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','8','8','8 (한국사이즈 270)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','8.5','8.5','8.5 (한국사이즈 275)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','9','9','9 (한국사이즈 280)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','9.5','9.5','9.5 (한국사이즈 285)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','99','99','One Size','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','L2','L2','XXL(한국사이즈XXXL)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','m42','42','42 (27~28 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','m44','44','44 (29~30 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','m46','46','46 (30~31 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','m48','48','48 (32~33 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','m50','50','50 (33~34 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','m52','52','52 (34~35 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','w32','32','32 (23~24 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','w34','34','34 (25~26 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','w36','36','36 (27~28 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','w38','38','38 (29~30 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','w40','40','40 (31~32 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','w42','42','42 (33~34 inch)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','X2','X2','XXS(한국사이즈XS)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','XL','XL','XL(한국사이즈XXL)','','','Y','0','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRD_CD_SIZE_MATCH','XS','XS','XS(한국사이즈S)','','','Y','0','ceduce','본사_김용남',now(),now());

-- code 데이터 추가 행사코드 : SR_CODE
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_CODE','10','요청','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_CODE','30','이동','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_CODE','40','완료','','','','Y','3','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 행사코드 : SR_CODE
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_CODE','[매장관리]반품상태','sr_code','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 행사코드 : SR_REASON
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_REASON','01','정상반품','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_REASON','02','불량반품','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_REASON','03','시즌반품','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_REASON','04','회수반품','','','','Y','4','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 행사코드 : SR_REASON
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('SR_REASON','[매장관리]반품사유','sr_reason','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 행사코드 : PRODUCT_STOCK_TYPE
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','1','입고','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','2','주문','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','5','교환','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','6','환불','','','','Y','4','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','7','주문취소','','','','Y','5','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','9','상품반품','','','','Y','6','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','11','반품','','','','Y','7','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','14','LOSS','','','','Y','8','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','15','RT','','','','Y','9','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','16','상품이동','','','','Y','10','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','17','출고','','','','Y','11','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 행사코드 : PRODUCT_STOCK_TYPE
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('PRODUCT_STOCK_TYPE','[매장관리]재고분류','product_stock_type','Y',NULL,'0','','본사_김용남',now(),now());

-- code 데이터 추가 입고상태 : STOCK_ORDER_STATE
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STOCK_ORDER_STATE','-10','입고취소','','','','Y','1','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STOCK_ORDER_STATE','10','입고대기','','','','Y','2','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STOCK_ORDER_STATE','20','입고처리중','','','','Y','3','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STOCK_ORDER_STATE','30','입고완료','','','','Y','4','ceduce','본사_김용남',now(),now());
insert into `code` (`code_kind_cd`, `code_id`, `code_val`, `code_val2`, `code_val3`, `code_val_eng`, `use_yn`, `code_seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STOCK_ORDER_STATE','40','원가확정','','','','Y','5','ceduce','본사_김용남',now(),now());

-- code_kind 데이터 추가 입고상태 : STOCK_ORDER_STATE
insert into `code_kind` (`code_kind_cd`, `code_kind_nm`, `code_kind_nm_eng`, `use_yn`, `type`, `seq`, `admin_id`, `admin_nm`, `rt`, `ut`) values('STOCK_ORDER_STATE','[매장관리]입고상태','stock_order_state','Y',NULL,'0','','본사_김용남',now(),now());

--
-- 테이블 데이터 추가 종료
--
