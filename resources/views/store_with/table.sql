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
	KEY `idx_storecd` (`store_cd`, `prd_cd`)
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
	KEY `idx_storagecd` (`storage_cd`, `prd_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고 물류별';

-- 오프라인 출고
CREATE TABLE `product_stock_release` (
	`idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
	`document_number` INT(11) NOT NULL COMMENT '전표번호',
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
	`document_number` INT(11) NOT NULL COMMENT '전표번호',
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
	`store_channel` varchar(10) NOT NULL COMMENT '판매채널코드'
	`grade_cd` varchar(2) DEFAULT NULL COMMENT '매장등급 - code : store_grade : grade_cd',
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
	`account_yn` char(1) DEFAULT 'N' COMMENT '정산관리여부',
	`com_id` varchar(20) DEFAULT '' COMMENT '업체ID',
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
  `sale_memo` varchar(255) DEFAULT NULL COMMENT '동종업계 월별 메모',
  `admin_id` varchar(50) DEFAULT NULL COMMENT '작성자',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '최근 수정일',
  PRIMARY KEY (`idx`),
  UNIQUE KEY `row_key` (`store_cd`,`competitor_cd`,`sale_date`),
  KEY `idx` (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=12525 DEFAULT CHARSET=utf8;

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

-- 판매 유형 관리 - 브랜드
CREATE TABLE `sale_type_brand` (
	`idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
	`sale_type_cd` varchar(30) NOT NULL COMMENT '판매유형 idx',
	`brand` varchar(30) NOT NULL COMMENT '브랜드',
	`brand_nm` varchar(100) NOT NULL COMMENT '브랜드명',
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
	`fee_10_info_over_yn` CHAR(1) DEFAULT 'N' COMMENT '특가기준(%) 초과여부(이상:’N’, 초과:‘Y’)',
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
	`fixed_return_price` int(11) NOT NULL COMMENT '확정단가',
	`fixed_return_qty` int(11) NOT NULL COMMENT '확정수량',
	`fixed_comment` varchar(255) COMMENT '확정메모',
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
	`loss_qty` int(11) COMMENT 'LOSS 수량',
	`loss_rec_qty` int(11) COMMENT 'LOSS인정수량',
	`loss_price` int(11) COMMENT 'LOSS 금액',
	`loss_price2` int(11) COMMENT 'LOSS 현재가 금액',
	`loss_tag_price` int(11) COMMENT 'LOSS TAG가 금액',
	`rt` datetime DEFAULT NULL COMMENT '등록일자',
	`ut` datetime DEFAULT NULL COMMENT '수정일자',
	`admin_id` varchar(30) DEFAULT NULL COMMENT '관리자아이디',
	PRIMARY KEY (`sc_prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 매장마감
CREATE TABLE `store_account_closed` (
	`idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '정산번호',
	`store_cd` varchar(30) NOT NULL DEFAULT '' COMMENT '매장코드',
	`sday` varchar(8) NOT NULL DEFAULT '' COMMENT '마감기준시작일',
	`eday` varchar(8) NOT NULL DEFAULT '' COMMENT '마감기준종료일',
	`sale_amt` int(11) DEFAULT '0' COMMENT '판매금액',
	`clm_amt` int(11) DEFAULT '0' COMMENT '클레임금액',
	`dc_amt` int(11) DEFAULT '0' COMMENT '할인 금액',
	`coupon_amt` int(11) DEFAULT '0' COMMENT '쿠폰금액',
	`allot_amt` int(11) DEFAULT '0' COMMENT '(본사부담)쿠폰금액',
	`dlv_amt` int(11) DEFAULT '0' COMMENT '배송비',
	`etc_amt` int(11) DEFAULT '0' COMMENT '기타정산액',
	`sale_net_taxation_amt` int(11) DEFAULT '0' COMMENT '과세',
	`sale_net_taxfree_amt` int(11) DEFAULT '0' COMMENT '비과세',
	`sale_net_amt` int(11) DEFAULT '0' COMMENT '매출금액',
	`tax_amt` int(11) DEFAULT '0' COMMENT '부가세',
	`fee_rate_JS1` decimal(10,2) DEFAULT '0' COMMENT '정상1 수수료율',
	`fee_JS1` int(11) DEFAULT '0' COMMENT '정상1 수수료',
	`fee_rate_JS2` decimal(10,2) DEFAULT '0' COMMENT '정상2 수수료율',
	`fee_JS2` int(11) DEFAULT '0' COMMENT '정상2 수수료',
	`fee_rate_JS3` decimal(10,2) DEFAULT '0' COMMENT '정상3 수수료율',
	`fee_JS3` int(11) DEFAULT '0' COMMENT '정상3 수수료',
	`fee_rate_TG` decimal(10,2) DEFAULT '0' COMMENT '특가 수수료율',
	`fee_TG` int(11) DEFAULT '0' COMMENT '특가 수수료',
	`fee_rate_YP` decimal(10,2) DEFAULT '0' COMMENT '용품 수수료율',
	`fee_YP` int(11) DEFAULT '0' COMMENT '용품 수수료',
	`fee_rate_OL` decimal(10,2) DEFAULT '0' COMMENT '특가(온라인) 수수료율',
	`fee_OL` int(11) DEFAULT '0' COMMENT '특가(온라인) 수수료',
	`fee` int(11) DEFAULT '0' COMMENT '중간관리자 수수료합계',
	`extra_amt` int(11) DEFAULT '0' COMMENT '기타재반금액',
	`fee_net` int(11) DEFAULT '0' COMMENT '수수료총계 (기타재반포함)',
	`closed_yn` char(1) DEFAULT 'N' COMMENT '마감여부',
	`closed_date` datetime DEFAULT NULL COMMENT '마감일시',
	`pay_day` varchar(8) DEFAULT NULL COMMENT '지급일',
	`tax_no` double DEFAULT NULL COMMENT '세금계산서번호',
	`admin_id` varchar(50) DEFAULT NULL COMMENT '관리자아이디',
	`admin_nm` varchar(50) DEFAULT NULL COMMENT '관리자명',
	`rt` datetime DEFAULT NULL COMMENT '등록일시',
	`ut` datetime DEFAULT NULL COMMENT '수정일시',
	`sale_fee` int(11) DEFAULT '0' COMMENT '판매 수수료',
	`fee_dc_amt` int(11) DEFAULT '0' COMMENT '수수료할인',
	`acc_amt` int(11) DEFAULT '0' COMMENT '정산금액',
	PRIMARY KEY (`idx`),
	UNIQUE KEY `store_cd` (`store_cd`,`sday`,`eday`),
	KEY `sdate` (`sday`,`eday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='매장 마감'

-- 매장마감 상세
CREATE TABLE `store_account_closed_list` (
	`idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
	`acc_idx` int(11) NOT NULL DEFAULT '0' COMMENT '정산번호',
	`type` varchar(10) NOT NULL DEFAULT '' COMMENT '주문타입 - order_opt_wonga: ord_state (판매: 30, 교환: 60, 환불: 61, 판매 및 교환: 90, 판매 및 환불: 91, 기타: 00)',
	`sale_type` varchar(10) DEFAULT NULL COMMENT '판매유형 (정상(JS) / 특가(TG) / 용품(YP) / 특약(온라인)(OL))',
	`ord_opt_no` int(11) NOT NULL DEFAULT '0' COMMENT '주문일련번호',
	`state_date` varchar(8) NOT NULL DEFAULT '' COMMENT '마감대상일자 - 출고완료일, 클레임완료일 등',
	`qty` int(11) DEFAULT NULL COMMENT '수량',
	`sale_amt` int(11) DEFAULT '0' COMMENT '판매금액',
	`clm_amt` int(11) DEFAULT '0' COMMENT '클레임금액',
	`dc_amt` int(11) DEFAULT '0' COMMENT '할인 금액',
	`coupon_amt` int(11) DEFAULT '0' COMMENT '쿠폰금액',
	`allot_amt` int(11) DEFAULT '0' COMMENT '(본사부담)쿠폰금액',
	`dlv_amt` int(11) DEFAULT '0' COMMENT '배송비',
	`etc_amt` int(11) DEFAULT '0' COMMENT '기타정산액',
	`sale_net_taxation_amt` int(11) DEFAULT '0' COMMENT '과세',
	`sale_net_taxfree_amt` int(11) DEFAULT '0' COMMENT '비과세',
	`sale_net_amt` int(11) DEFAULT '0' COMMENT '매출액',
	`tax_amt` decimal(10,2) DEFAULT '0' COMMENT '부가세',
	`fee_ratio` float DEFAULT '0' COMMENT '판매유형별 중간관리자 수수료율(%)',
	`fee` int(11) DEFAULT '0' COMMENT '판매유형별 중간관리자 수수료',
	`memo` varchar(255) DEFAULT NULL COMMENT '메모',
	`sale_fee` int(11) DEFAULT '0' COMMENT '판매 수수료',
	`sale_clm_amt` int(11) DEFAULT NULL COMMENT '판매 + 클레임',
	`fee_dc_amt` int(11) DEFAULT '0' COMMENT '세금할인액',
	`fee_net` int(11) DEFAULT NULL COMMENT '수수료',
	`acc_amt` int(11) DEFAULT '0' COMMENT '정산금액',
	PRIMARY KEY (`idx`),
	KEY `idx_accidx` (`acc_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='매장 마감내역'

-- 기타정산
CREATE TABLE `store_account_etc` (
	`idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
	`acc_list_idx` VARCHAR(6) DEFAULT NULL COMMENT '정산일련번호 (store_account_closed_list : idx)',
	`ymonth` VARCHAR(6) DEFAULT NULL COMMENT '정산연월',
	`etc_day` VARCHAR(8) DEFAULT NULL COMMENT '기타정산일자',
	`store_cd` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '매장코드',
	`ord_opt_no` INT(11) DEFAULT NULL COMMENT '주문일련번호',
	`etc_amt` INT(11) DEFAULT NULL COMMENT '기타정산액',
	`etc_memo` varchar(255) DEFAULT NULL COMMENT '기타정산메모',
	`admin_id` VARCHAR(20) DEFAULT NULL COMMENT '관리자아이디',
	`admin_nm` VARCHAR(20) DEFAULT NULL COMMENT '관리자명',
	`rt` DATETIME DEFAULT NULL COMMENT '등록일자',
	`ut` DATETIME DEFAULT NULL COMMENT '수정일자',
	PRIMARY KEY (`idx`),
	KEY `acc_list_idx` (`acc_list_idx`),
	KEY `idx_etc_day` (`etc_day`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장 기타정산액';

-- 매장기타재반자료
CREATE TABLE `store_account_extra` (
	`idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
	`ymonth` VARCHAR(6) NOT NULL COMMENT '정산연월',
	`store_cd` VARCHAR(30) NOT NULL COMMENT '매장코드',
	`extra_amt` INT(11) DEFAULT 0 COMMENT '기타재반자료 총합계',
	`admin_id` VARCHAR(30) DEFAULT NULL COMMENT '관리자아이디',
	`rt` DATETIME DEFAULT NULL COMMENT '등록일자',
	PRIMARY KEY (`idx`),
	KEY `ymonth` (`ymonth`, `store_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장기타재반자료'

-- 매장기타재반자료 내역
CREATE TABLE `store_account_extra_list` (
	`idx` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'identify',
	`ext_idx` INT(11) NOT NULL COMMENT 'store_account_extra : idx',
	`type` VARCHAR(10) NOT NULL COMMENT 'code : STORE_ACC_EXTRA_TYPE',
	`prd_cd` VARCHAR(50) DEFAULT NULL COMMENT '원부자재코드 (type이 ’S’ or ‘G’ 일 때, 해당 사은품/소모품의 원부자재코드)',
	`prd_nm` VARCHAR(100) DEFAULT NULL COMMENT '원부자재명 (type이 ’S’ or ‘G’ 일 때, 해당 사은품/소모품의 원부자재명)',
	`extra_amt` INT(11) DEFAULT 0 COMMENT '기타재반자료금액',
	PRIMARY KEY (`idx`),
	KEY `ext_idx` (`ext_idx`, `type`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장기타재반자료 내역'

-- 매장기타재반자료 타입
CREATE TABLE `store_account_extra_type` (
	`type_cd` varchar(30) NOT NULL COMMENT '타입코드',
	`type_nm` varchar(30) NOT NULL COMMENT '타입명',
	`entry_cd` varchar(30) DEFAULT NULL COMMENT '상위타입코드',
	`payer` varchar(10) DEFAULT NULL COMMENT '비용부담주체 (C(본사) / S(매장))',
	`except_vat_yn` char(1) DEFAULT NULL COMMENT '세금제외여부',
	`total_include_yn` char(1) DEFAULT NULL COMMENT '합계포함여부',
	`has_child_yn` char(1) DEFAULT 'N' COMMENT '하위타입유무',
	`use_yn` char(1) DEFAULT 'Y' COMMENT '사용여부',
	`seq` int(11) DEFAULT 0 COMMENT '정렬순서',
	`rt` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '등록일자',
	`ut` datetime DEFAULT NULL COMMENT '수정일자',
	PRIMARY KEY (`type_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='매장기타재반자료 타입관리'

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
	`del_yn` char(1) DEFAULT 'N' NOT NULL COMMENT '알림 삭제',
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

-- 고객수선2
CREATE TABLE `repair_service` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '접수번호',
  `receipt_date` date DEFAULT NULL COMMENT '접수일자',
  `as_state` smallint(6) DEFAULT NULL COMMENT '수선진행상태 (10 : 수선요청, 11 : 불량요청, 12 : 본사심의요청, 20 : 수선접수, 30 : 수선진행, 40 : 수선완료, 50 : 불량)',
  `store_cd` varchar(20) DEFAULT NULL COMMENT '매장코드',
  `as_type` char(1) DEFAULT NULL COMMENT '접수구분 (1 : 매장접수(A/S), 2 : 매장접수(불량), 3 : 매장접수(심의), 4 : 본사A/S접수/진행, 5 : 본사A/S완료, 6 : 본사불량)',
  `customer_no` varchar(30) DEFAULT NULL COMMENT '고객아이디',
  `customer` varchar(20) DEFAULT NULL COMMENT '고객명',
  `mobile` varchar(20) DEFAULT NULL COMMENT '핸드폰번호',
  `zipcode` varchar(10) DEFAULT NULL COMMENT '우편번호',
  `addr1` varchar(50) DEFAULT NULL COMMENT '주소1',
  `addr2` varchar(50) DEFAULT NULL COMMENT '주소2',
  `prd_cd` varchar(50) DEFAULT NULL COMMENT '바코드',
  `goods_nm` varchar(100) DEFAULT NULL COMMENT '수선 상품명',
  `color` varchar(30) DEFAULT NULL COMMENT '컬러',
  `size` varchar(30) DEFAULT NULL COMMENT '사이즈',
  `qty` int(11) DEFAULT NULL COMMENT '수량',
  `is_free` char(1) DEFAULT NULL COMMENT '수선 유료구분',
  `as_amt` int(11) DEFAULT NULL COMMENT '수선 비용',
  `content` text COMMENT '수선 내용',
  `h_receipt_date` date DEFAULT NULL COMMENT '본사접수일',
  `end_date` date DEFAULT NULL COMMENT '수선완료일',
  `err_date` date DEFAULT NULL COMMENT '불량등록일',
  `h_content` text COMMENT '본사 설명',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '수정일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


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


-- 그룹관리(메뉴권한)
CREATE TABLE `store_group_authority` (
  `group_no` int(11) NOT NULL DEFAULT '0' COMMENT '메뉴번호',
  `wonga_yn` char(1) DEFAULT 'Y' COMMENT '원가보여주기',
  `other_store_yn` char(1) DEFAULT 'Y' COMMENT '타매장감추기',
  `release_price_yn` char(1) DEFAULT 'Y' COMMENT '출고가보여주기',
  `pos_use_yn` char(1) DEFAULT 'Y' COMMENT 'POS 사용여부',
  `auth_store_yn` char(1) DEFAULT 'N' COMMENT '매장권한',
  `auth_storage_yn` char(1) DEFAULT 'N' COMMENT '창고권한',
  PRIMARY KEY (`group_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--  로그관리
CREATE TABLE `store_log` (
  `menu_no` int(11) DEFAULT NULL COMMENT '메뉴번호',
  `pid` varchar(20) DEFAULT NULL COMMENT '프로그램ID',
  `cmd` varchar(100) DEFAULT NULL COMMENT '명령어',
  `menu_nm` varchar(255) DEFAULT NULL COMMENT '메뉴명',
  `exec_time` float DEFAULT NULL COMMENT '사용시간',
  `id` varchar(15) DEFAULT NULL COMMENT '아이디',
  `ip` varchar(30) DEFAULT NULL COMMENT 'ip',
  `name` varchar(20) DEFAULT NULL COMMENT '관리자명',
  `log_time` datetime DEFAULT NULL COMMENT '로그시간',
  KEY `log_time` (`log_time`),
  KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='관리자 로그정보';


-- 온라인 재고 매핑
CREATE TABLE `bizest_stock_conf` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `default_storage_cd` varchar(30) DEFAULT NULL COMMENT '대표창고 코드',
  `default_storage_buffer` int(11) DEFAULT NULL COMMENT '대표창고 버퍼링값',
  `online_storage_cd` varchar(30) DEFAULT NULL COMMENT '온라인창고 코드',
  `online_storage_buffer` int(11) DEFAULT NULL COMMENT '온라인창고 버퍼링값',
  `store_buffer_kind` char(1) DEFAULT 'A' COMMENT '매장 버퍼링 유형 (A : 통합, S : 개별)',
  `store_tot_buffer` int(11) DEFAULT NULL COMMENT '매장 통합버퍼링 값',
  `price_apply_yn` char(1) DEFAULT 'N' COMMENT '가격 반영 여부',
  `id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '수정일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 온라인 재고 매핑 매장관리
CREATE TABLE `bizest_stock_store` (
  `store_cd` varchar(30) NOT NULL COMMENT '매장코드',
  `store_use_yn` char(1) DEFAULT 'N' COMMENT '매장사용여부',
  `buffer_cnt` int(11) DEFAULT NULL COMMENT '버퍼링 수',
  `id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '수정일',
  PRIMARY KEY (`store_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 온라인 재고 매핑 예외관리
CREATE TABLE `bizest_stock_exp_product` (
  `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
  `storage_limit_qty` int(11) DEFAULT NULL COMMENT '창고 제한수',
  `store_limit_qty` int(11) DEFAULT NULL COMMENT '매장 제한수',
  `comment` varchar(255) DEFAULT NULL COMMENT '정보',
  `id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '수정일',
  PRIMARY KEY (`prd_cd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 온라인 재고 매핑 로그관리
CREATE TABLE `bizest_stock_log` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `price_apply_yn` char(1) DEFAULT NULL COMMENT '가격 반영 여부',
  `store_buffer_kind` char(1) DEFAULT NULL COMMENT '매장 버퍼링 유형 (A : 통합, S : 개별)',
  `store_cnt` int(11) DEFAULT NULL COMMENT '사용 매장 수',
  `match_y_cnt` int(11) DEFAULT NULL COMMENT '매칭상품 수량',
  `match_n_cnt` int(11) DEFAULT NULL COMMENT '비매칭상품 수량',
  `id` varchar(30) DEFAULT NULL COMMENT '관리자 아이디',
  `rt` datetime DEFAULT NULL COMMENT '등록일',
  `ut` datetime DEFAULT NULL COMMENT '수정일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 온라인 재고 매핑 로우 데이터
CREATE TABLE `bizest_stock_data` (
	`idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identify',
	`stock_log_cd` int(11) NOT NULL COMMENT 'bizest_stock_log id',
	`stock_cd` varchar(30) NOT NULL COMMENT '재고 매장/창고',
	`prd_cd` varchar(50) NOT NULL COMMENT '상품 코드',
	`goods_no` int(11) DEFAULT NULL COMMENT '상품 번호',
	`goods_opt` varchar(100) DEFAULT NULL COMMENT '상품 옵션',
	`qty` int(11) NOT NULL COMMENT '수량',
	PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 상품가격 변경 마스터
CREATE TABLE `product_price` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
  `change_date` varchar(30) NOT NULL COMMENT '변경날짜',
  `change_kind` char(1) NOT NULL COMMENT '변경종류 (퍼센트 : P , 원 : W)',
  `change_val` int(11) NOT NULL COMMENT '변경금액(율)',
  `apply_yn` char(1) NOT NULL DEFAULT 'N' COMMENT '가격적용유무 (Y/N)',
  `change_cnt` int(11) NOT NULL COMMENT '적용상품수',
  `change_type` char(1) NOT NULL DEFAULT 'R' COMMENT '가격변경 타입 (예약 : R , 즉시 : A)'
  `admin_id` varchar(50) NOT NULL COMMENT '관리자아이디',
  `rt` datetime NOT NULL COMMENT '등록일자',
  `ut` datetime DEFAULT NULL COMMENT '수정일자',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- 상품가격 변경 리스트
CREATE TABLE `product_price_list` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
  `product_price_cd` int(11) NOT NULL COMMENT '상품가격코드 (참조 : product_price -> idx)',
  `prd_cd` varchar(50) NOT NULL COMMENT '상품코드',
  `org_price` int(11) NOT NULL COMMENT '기존가격',
  `change_price` int(11) NOT NULL COMMENT '변경가격',
  `admin_id` varchar(50) NOT NULL COMMENT '관리자아이디',
  `rt` datetime NOT NULL COMMENT '등록일자',
  `ut` datetime NOT NULL COMMENT '수정일자',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- 검색어 바로가기
CREATE TABLE `search_shortcut` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '일련번호',
  `kwd` varchar(100) NOT NULL COMMENT '검색어',
  `url` varchar(100) NOT NULL COMMENT 'URL',
  `disp_yn` varchar(1) DEFAULT 'Y' COMMENT '검색창 출력',
  `pv` int(11) NOT NULL DEFAULT '0' COMMENT '검색횟수',
  `use_yn` varchar(1) DEFAULT 'Y' COMMENT '사용여부',
  `st` datetime DEFAULT NULL COMMENT '최근검색일시',
  `rt` datetime DEFAULT NULL COMMENT '등록일시',
  `ut` datetime DEFAULT NULL COMMENT '변경일시',
  PRIMARY KEY (`idx`),
  UNIQUE KEY `kwd` (`kwd`),
  KEY `idx_disp_yn` (`disp_yn`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='검색어 바로가기';


-- 판매채널관리
CREATE TABLE `store_channel` (
  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `store_type` char(1) DEFAULT NULL COMMENT '구분 (C : 판매채널, S : 매장구분)',
  `store_channel_cd` varchar(10) DEFAULT NULL COMMENT '판매채널코드',
  `store_channel` varchar(30) DEFAULT NULL COMMENT '판매채널명',
  `store_kind_cd` varchar(10) DEFAULT NULL COMMENT '매장구분코드',
  `store_kind` varchar(30) DEFAULT NULL COMMENT '매장 구분명',
  `dep` int(11) DEFAULT NULL COMMENT '뎁스',
  `seq` int(11) DEFAULT NULL COMMENT '순서',
  `use_yn` char(1) DEFAULT NULL COMMENT '사용여부',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

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
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `dlv_place_type` VARCHAR(10) NULL COMMENT '발송처타입(온라인주문)' AFTER `pr_code`;
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `dlv_place_cd` VARCHAR(30) NULL COMMENT '발송처코드(온라인주문)' AFTER `dlv_place_type`;

ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `tax_fee`;
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD COLUMN `store_cd` VARCHAR(30) NULL COMMENT '매장코드' AFTER `prd_cd`;

ALTER TABLE `bizest_smart`.`order_opt` ADD INDEX `idx_store_cd_orddate` (`store_cd`, `ord_date`);
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD INDEX `idx_ord_state_date` (`ord_state_date`,`ord_state,store_cd`);
ALTER TABLE `bizest_smart`.`order_opt_wonga` ADD INDEX `idx_prdcd` (`prd_cd`);


ALTER TABLE `bizest_smart`.`mgr_user` ADD COLUMN `confirm_yn` CHAR(1) NULL COMMENT '승인여부' AFTER `md_yn`;
ALTER TABLE `bizest_smart`.`mgr_user` ADD COLUMN `store_nm` VARCHAR(100) NULL COMMENT '매장명' AFTER `store_cd`;
ALTER TABLE `bizest_smart`.`mgr_user` ADD COLUMN `account_yn` CHAR(1) DEFAULT 'Y' NULL COMMENT '정산/마감 확인여부' AFTER `store_nm`;

ALTER TABLE `bizest_smart`.`notice_store_detail` ADD COLUMN `check_date` DATETIME NULL COMMENT '공지사항확인일시' AFTER `check_yn`;

-- 쿠폰
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `pub_time` INT(11) NULL DEFAULT NULL COMMENT '발행시점' AFTER `coupon_type`;
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `pub_type` VARCHAR(1) NULL DEFAULT 'N' COMMENT '발행방법(매월:M,특정일:D,특정요일:W)' AFTER `pub_to_date`;
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `pub_day` INT(11) NULL DEFAULT NULL COMMENT '발행가능일(일자/요일)' AFTER `pub_type`;
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `use_date_type` CHAR(1) NOT NULL DEFAULT 'S' COMMENT '유효기간 유형' AFTER `pub_day`;
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `use_date_alarm_yn` CHAR(1) NULL DEFAULT NULL COMMENT '유효기간 알림 사용 여부' AFTER `use_date_type`;
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `use_date_alarm_day` INT(11) NULL DEFAULT NULL COMMENT '유효기간 알림 기간(일)' AFTER `use_date_alarm_yn`;
ALTER TABLE `bizest_smart`.`coupon` ADD COLUMN `use_date` VARCHAR(4) NOT NULL DEFAULT '' COMMENT '유효기간 쿠폰발급일 기준(설정일/발급일 : S/P)' AFTER `use_to_date`;

ALTER TABLE `bizest_smart`.`coupon_member` ADD COLUMN `use_to_date` VARCHAR(8) NULL DEFAULT NULL COMMENT '사용가능 일시' AFTER `down_date`;

-- 물류 창고 그룹 포함 여부
ALTER TABLE bizest_smart.mgr_user ADD COLUMN logistics_group_yn varchar(1) DEFAULT 'N' NULL COMMENT '물류창고 그룹 포함 여부';
-- 공지사항 분기 코드값
ALTER TABLE bizest_smart.mgr_user ADD COLUMN store_notice_type varchar(30) NOT NULL COMMENT '공지사항 종류 (01 - 메인공지사항 / 02 - vmd게시판)';
-- 공지사항 첨부파일
ALTER TABLE bizest_smart.mgr_user ADD COLUMN attach_file_url  varchar(2000) DEFAULT NULL COMMENT '첨부파일  url';

-- 사은품
ALTER TABLE `bizest_smart`.`gift` ADD COLUMN `dp_soldout_yn` CHAR(1) NULL DEFAULT 'N' COMMENT '품절시 출력여부' AFTER `unlimited_yn`;
ALTER TABLE `bizest_smart`.`gift` ADD COLUMN `gift_price` INT(11) NULL DEFAULT NULL COMMENT '사은품가격' AFTER `apply_amt`;
ALTER TABLE `bizest_smart`.`gift` ADD COLUMN `apply_group` VARCHAR(20) NULL DEFAULT NULL COMMENT '증정대상' AFTER `apply_com`;


-- 검색
ALTER TABLE `bizest_smart`.'search' ADD COLUMN `synonym` VARCHAR(50) NULL COMMENT '동의어' AFTER `ex_pop_yn`;
--
-- 기존 테이블 컬럼 추가 종료
--

--
-- 테이블 데이터 추가 시작
--

-- `store_account_extra_type` 매장기타재반자료 타입 데이터 추가
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('M', '마일리지', NULL, NULL, NULL, NULL, 'Y', 'Y', '0', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P', '인건비', NULL, NULL, NULL, NULL, 'Y', 'Y', '1', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('S', '매장운영비용', NULL, 'S', NULL, NULL, 'Y', 'Y', '2', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('G', '사은품', NULL, 'S', 'N', 'Y', 'N', 'Y', '3', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('E', '소모품', NULL, 'S', 'N', 'Y', 'N', 'Y', '4', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('O', '기타운영경비', NULL, 'C', NULL, NULL, 'Y', 'Y', '5', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('M1', '마일리지', 'M', NULL, 'Y', 'Y', 'N', 'Y', '0', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P1', '온라인RT', 'P', NULL, 'N', 'N', 'N', 'Y', '1', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P2', '온라인반송', 'P', NULL, 'N', 'N', 'N', 'Y', '2', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P3', '온라인', 'P', NULL, 'N', 'Y', 'N', 'Y', '3', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P4', '인센티브', 'P', NULL, 'N', 'Y', 'N', 'Y', '4', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P5', '패널티', 'P', NULL, 'N', 'Y', 'N', 'Y', '5', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('P6', '기타수수료', 'P', NULL, 'N', 'Y', 'N', 'Y', '6', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('S1', '전화요금', 'S', 'S', 'N', 'Y', 'N', 'Y', '7', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('S2', '인터넷', 'S', 'S', 'N', 'Y', 'N', 'Y', '8', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('S3', '본사수선비', 'S', 'S', 'Y', 'Y', 'N', 'Y', '9', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('S4', '외부창고/보안비', 'S', 'S', 'N', 'Y', 'N', 'Y', '10', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('O1', '외부창고', 'O', 'C', 'N', 'N', 'N', 'Y', '11', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('O2', '추가지급', 'O', 'C', 'N', 'Y', 'N', 'Y', '12', now());
INSERT INTO `bizest_smart`.`store_account_extra_type` (`type_cd`, `type_nm`, `entry_cd`, `payer`, `except_vat_yn`, `total_include_yn`, `has_child_yn`, `use_yn`, `seq`, `rt`) VALUES ('O3', '사용경비(기타)', 'O', 'C', 'N', 'Y', 'N', 'Y', '13', now());


-- 공지사항 코드값 등록
INSERT INTO bizest_smart.code
(code_kind_cd, code_id, code_val, code_val2, code_val3, code_val_eng, use_yn, code_seq, admin_id, admin_nm, rt, ut)
VALUES('STORE_NOTICE_TYPE', '01', 'notice', '', '', '', 'Y', 1, 'sm_dh', '이두희', now(), now());

INSERT INTO bizest_smart.code
(code_kind_cd, code_id, code_val, code_val2, code_val3, code_val_eng, use_yn, code_seq, admin_id, admin_nm, rt, ut)
VALUES('STORE_NOTICE_TYPE', '02', 'vmd', '', '', '', 'Y', 2, 'sm_dh', '이두희', now(), now());


-- 공지사항 메뉴 그룹 최신화
INSERT INTO bizest_smart.store_controller
(entry, pid, seq, lev, kor_nm, eng_nm, kind, id, target, `action`, btype, state, sys_menu, regi_date, ut, is_del, is_part_role, icon)
VALUES(1, 'COMM', 11, 1, '공지사항', '곤지시항', 'N', 'sm_dh', null, null, null, 4, 'U', now(), now(), 0, null, 'bx-clipboard');


INSERT INTO bizest_smart.store_controller
(entry, pid, seq, lev, kor_nm, eng_nm, kind, id, target, `action`, btype, state, sys_menu, regi_date, ut, is_del, is_part_role, icon)
VALUES(72, 'comm01', 1, 2, '매장 공지사항', '매장 공지시항', 'M', 'sm_dh', null, '/store/community/comm01/notice', null, 4, 'U', now(), now(), 0, null, null);

INSERT INTO bizest_smart.store_controller
(entry, pid, seq, lev, kor_nm, eng_nm, kind, id, target, `action`, btype, state, sys_menu, regi_date, ut, is_del, is_part_role, icon)
VALUES(72, 'comm01', 2, 2, 'VMD 게시판', 'VMD 게시판', 'M', 'sm_dh', null, '/store/community/comm01/vmd', null, 4, 'U', now(), now(), 0, null, null);
--
-- 테이블 데이터 추가 종료
--
