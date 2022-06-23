
-- 오프라인 재고
CREATE TABLE `product_stock` (
                                 `goods_no` INT(11) NOT NULL DEFAULT '0' COMMENT '상품번호',
                                 `prd_cd` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '상품코드',
                                 `wqty` INT(11) DEFAULT NULL COMMENT '보유재고',
                                 `goods_opt` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '상품옵션명',
                                 `use_yn` CHAR(1) DEFAULT NULL COMMENT '사용여부',
                                 `rt` DATETIME DEFAULT NULL COMMENT '등록일시',
                                 `ut` DATETIME DEFAULT NULL COMMENT '변경일시',
                                 PRIMARY KEY (`goods_no`,`prd_cd`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='상품재고';

-- 주문에 '상품코드' 추가
ALTER TABLE `bizest_smart`.`order_opt` ADD COLUMN `prd_cd` VARCHAR(50) NULL COMMENT '상품코드' AFTER `out_ord_opt_no`;
