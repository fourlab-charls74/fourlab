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
	`prd_cd_p` varchar(30) NOT NULL COMMENT '상품코드',
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
	PRIMARY KEY (`prd_cd`),
	KEY `idx_prdcdp` (`prd_cd_p`)
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
	`sale_place_match_yn` char(1) DEFAULT 'N' COMMENT '업체매칭여부',
	`reg_date` datetime DEFAULT NULL COMMENT '등록일',
	`mod_date` datetime DEFAULT NULL COMMENT '수정일',
	`admin_id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
	PRIMARY KEY (`store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장이미지
CREATE TABLE `store_img` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
  `store_cd` varchar(30) NOT NULL COMMENT '매장 코드',
  `seq` int(11) NOT NULL DEFAULT '0' COMMENT '이미지순서',
  `img_url` varchar(255) NOT NULL COMMENT '이미지 경로',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '수정일',
  `admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
  PRIMARY KEY (`idx`)
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
	`online_yn` char(1) DEFAULT 'N' COMMENT '온라인창고여부',
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

-- (온라인)주문접수
CREATE TABLE `order_receipt` (
	`or_cd` INT(11) NOT NULL AUTO_INCREMENT COMMENT '주문접수코드',
	`type` CHAR(5) NOT NULL COMMENT '주문구분 (온라인/오프라인 : ON/OFF)',
	`rel_order` VARCHAR(30) NOT NULL COMMENT '출고차수 - code(REL_ORDER) : O_01 ~ O_10',
	`req_rt` DATETIME DEFAULT NULL COMMENT '접수일시',
	`req_id` VARCHAR(50) DEFAULT NULL COMMENT '점수자',
	`fin_rt` DATETIME DEFAULT NULL COMMENT '처리일시',
	`fin_id` VARCHAR(50) DEFAULT NULL COMMENT '처리자',
	`ut` DATETIME DEFAULT NULL COMMENT '수정일시',
	PRIMARY KEY (`or_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='주문접수';

-- (온라인)주문접수 상품리스트
CREATE TABLE `order_receipt_product` (
	`or_prd_cd` INT(11) NOT NULL AUTO_INCREMENT COMMENT '주문접수일련코드',
	`or_cd` INT(11) NOT NULL COMMENT '주문접수코드',
	`ord_opt_no` INT(11) NOT NULL DEFAULT '0' COMMENT '주문일련번호',
	`prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
	`qty` INT(11) NOT NULL COMMENT '상품수량',
	`state` INT(11) NOT NULL COMMENT '상태(20(접수) / 30(처리) / -10(취소))',
	`dlv_location_type` VARCHAR(10) NOT NULL COMMENT '매장(STORE) / 창고(STORAGE)',
	`dlv_location_cd` VARCHAR(30) NOT NULL COMMENT '매장코드 or 창고코드',
	`comment` VARCHAR(255) DEFAULT NULL COMMENT '메모',
	`rt` DATETIME DEFAULT NULL COMMENT '등록일시',
	`ut` DATETIME DEFAULT NULL COMMENT '수정일시',
	PRIMARY KEY (`or_prd_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='주문접수 상품리스트';

-- 고객수선
CREATE TABLE `after_service` (
	`idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
	`receipt_date` date DEFAULT NULL COMMENT '접수일자',
	`customer_no` int(10) DEFAULT NULL COMMENT '고객번호',
	`customer` varchar(20) NOT NULL COMMENT '고객명',
	`as_type` varchar(20) NOT NULL COMMENT '수선구분(C: 고객수선, S: 매장수선, H: 본사수선)',
	`sale_date` date DEFAULT NULL COMMENT '판매일자',
	`h_receipt_date` date DEFAULT NULL COMMENT '본사접수일',
	`start_date` date DEFAULT NULL COMMENT '수선인도일',
	`due_date` date DEFAULT NULL COMMENT '수선예정일',
	`end_date` date DEFAULT NULL COMMENT '수선완료일',
	`receipt_no` int(10) DEFAULT NULL COMMENT '접수번호',
	`store_no` varchar(20) DEFAULT NULL COMMENT '매장번호(ex - A0000)',
	`store_nm` varchar(50) DEFAULT NULL COMMENT '매장명',
	`item` varchar(30) DEFAULT NULL COMMENT '품목',
	`product_cd` varchar(20) DEFAULT NULL COMMENT '제품코드',
	`product` varchar(40) DEFAULT NULL COMMENT '제품명',
	`color` varchar(10) DEFAULT NULL COMMENT '칼라',
	`size` varchar(10) DEFAULT NULL COMMENT '사이즈',
	`quantity` mediumint(9) DEFAULT NULL COMMENT '수량',
	`is_free` varchar(2) DEFAULT NULL COMMENT '수선유료구분(Y: 유료, N: 무료)',
	`charged_price` int(10) DEFAULT NULL COMMENT '유료수선금액',
	`free_price` int(10) DEFAULT NULL COMMENT '무료수선금액',
	`mobile` varchar(14) DEFAULT NULL COMMENT '연락처(핸드폰 번호)',
	`zipcode` mediumint(9) DEFAULT NULL COMMENT '우편번호',
	`addr1` varchar(50) DEFAULT NULL COMMENT '주소1',
	`addr2` varchar(50) DEFAULT NULL COMMENT '주소2',
	`content` text COMMENT '수선내용',
	`h_explain` text COMMENT '본사설명',
	`storing_cd` int(5) unsigned zerofill DEFAULT NULL COMMENT '입고처코드',
	`storing_nm` varchar(20) DEFAULT NULL COMMENT '입고처명',
	`as_cd` int(5) unsigned zerofill DEFAULT NULL COMMENT '수선처코드',
	`as_place` varchar(20) DEFAULT NULL COMMENT '수선처명',
	PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 메뉴관리
CREATE TABLE `store_controller` (
  `menu_no` int(11) NOT NULL AUTO_INCREMENT COMMENT '메뉴번호',
  `entry` int(11) DEFAULT NULL COMMENT '상위메뉴ID',
  `pid` varchar(30) DEFAULT NULL COMMENT '컨트롤러',
  `seq` int(11) DEFAULT NULL COMMENT '순서',
  `lev` int(11) DEFAULT NULL COMMENT '위치레벨',
  `kor_nm` varchar(30) DEFAULT NULL COMMENT '한글이름',
  `eng_nm` varchar(50) DEFAULT NULL COMMENT '영문이름',
  `kind` varchar(10) DEFAULT NULL COMMENT '종류',
  `id` varchar(15) DEFAULT NULL COMMENT '메뉴아이디',
  `target` varchar(100) DEFAULT NULL COMMENT '사용자',
  `action` varchar(100) DEFAULT NULL COMMENT '동작',
  `btype` int(11) DEFAULT NULL COMMENT '게시판유형',
  `state` smallint(6) DEFAULT NULL COMMENT '상태',
  `sys_menu` char(1) DEFAULT NULL COMMENT '시스템메뉴',
  `regi_date` datetime DEFAULT NULL COMMENT '등록일시',
  `ut` datetime DEFAULT NULL COMMENT '수정일시',
  `is_del` smallint(6) DEFAULT NULL COMMENT '삭제여부',
  `is_part_role` char(1) DEFAULT NULL COMMENT '부분권한여부',
  PRIMARY KEY (`menu_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






--
-- 기존 테이블 컬럼 추가 시작
--

-- 브랜드에 '브랜드 단축코드' 추가
ALTER TABLE `bizest_smart`.`brand` ADD COLUMN `br_cd` VARCHAR(3) NULL COMMENT '단축코드' AFTER `brand_nm_eng`;

-- 회원에 오프라인 회원 일괄 등록 필드 추가
ALTER TABLE `bizest_smart`.`member` ADD COLUMN `type` CHAR(1) NOT NULL DEFAULT 'N' COMMENT '회원가입 종류 - N : 일반, B : 일괄(XMD)' AFTER `prom_code`;
ALTER TABLE `bizest_smart`.`member` ADD COLUMN `store_nm` VARCHAR(100) DEFAULT NULL COMMENT '회원가입 매장명' AFTER `type`;
ALTER TABLE `bizest_smart`.`member` ADD COLUMN `store_cd` VARCHAR(30) DEFAULT NULL COMMENT '회원가입 매장코드' AFTER `store_nm`;

-- 관리자
ALTER TABLE `bizest_smart`.`mgr_user` ADD COLUMN `store_wonga_yn` CHAR(1) DEFAULT 'Y' NULL COMMENT '원가 노출 여부' AFTER `md_yn`;

-- 입고
ALTER TABLE `bizest_smart`.`stock_product` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `brand`;

-- 주문에 '상품코드' 추가
ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `out_ord_no`;
-- ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `sale_kind` VARCHAR(30) NULL COMMENT '판매유형' AFTER `store_cd`;
-- ALTER TABLE `bizest_smart`.`order_mst` ADD COLUMN `pr_code` VARCHAR(30) NULL COMMENT '행사구분' AFTER `sale_kind`;
-- 위 2개 항목 order_msg => order_opt 변경 (2022-08-23)

ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `out_ord_opt_no`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `prd_cd`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `sale_kind` VARCHAR(30) NULL COMMENT '판매유형' AFTER `store_cd`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `pr_code` VARCHAR(30) NULL COMMENT '행사구분' AFTER `sale_kind`;

ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `tax_fee`;
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `prd_cd`;

ALTER TABLE `bizest_smart`.`order_opt` ADD INDEX `idx_store_cd_orddate` (`store_cd`, `ord_date`);
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD INDEX `idx_ord_state_date` (`ord_state_date`,`ord_state,store_cd`);
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD INDEX `idx_prdcd` (`prd_cd`);



--
-- 기존 테이블 컬럼 추가 종료
--

--
-- 테이블 데이터 추가 시작
--

--
-- 테이블 데이터 추가 종료
--
