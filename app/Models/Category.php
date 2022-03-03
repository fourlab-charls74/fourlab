<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use Exception;

class Category
{
    private $user;
    private $type;
    private $code;

	/*
		기존 CategoryMulti Properties
	*/
	private $goods_no;
	private $goods_sub;

    function __construct($user, $type){
        $this->user = $user;
        $this->type = $type;
    }

    function SetCode( $code ){
        $this->code = $code;
    }

    public function AddProduct($goods_no,$disp_yn = "Y"){
        DB::table('category_goods')->insert([
            "cat_type" => $this->type,
            "d_cat_cd" => $this->code,
            "goods_no" => $goods_no,
            "goods_sub" => 0,
            "seq" => 0,
            "disp_yn" => $disp_yn,
            "admin_id" => $this->user["id"],
            "admin_nm" => $this->user["name"],
            "regi_date" => DB::raw('now()'),
        ]);
    }

	public function SetSeq($goods_no, string $string)
	{
		$categorys = DB::table("category_goods")
			->where("cat_type", "=", $this->type)
			->where("goods_no", "=", $goods_no)
			->where("goods_sub", "=", 0)->select("d_cat_cd")->get();

		foreach($categorys as $category){
			$d_cat_cd = $category->d_cat_cd;
			//echo "d_cat_cd : $d_cat_cd";
			$seq = DB::table("category_goods")
				->where("cat_type", "=", $this->type)
				->where("d_cat_cd", "=", $d_cat_cd)
				->where("goods_no", "=", $goods_no)
				->where("goods_sub", "=", 0)->max("seq");
			$this->SetCode($d_cat_cd);
			$this->UpdateSeq($goods_no, $seq+1);
		}
	}

    public function UpdateSeq($goods_no,$seq){
        DB::table('category_goods')
            ->where("cat_type","=",$this->type)
            ->where("d_cat_cd","=",$this->code)
            ->where("goods_no","=",$goods_no)
            ->where("goods_sub","=",0)
            ->update([
                "seq" => $seq
            ]);
    }


	/*
		Function: Location
		카테고리 코드의 경로 얻기

		Parameters:
			$code - 카테고리 코드

		Returns:
			$location - 카테고리 전체 경로
	*/
	public function Location( $code ){
		$sql = "
			select group_concat(d_cat_nm order by d_cat_cd  separator ' > ') as full_nm
			from category
			where cat_type = '".$this->type."'
				and instr('$code', d_cat_cd) = 1
		";
		$row = DB::selectOne($sql);

		return $row->full_nm;
	}

	/**************************************************************************************/
	/**  아래부터는 기존 CategoryMulti 모델에 있던 내용을 통합한 메소드입니다.	  			*/
	/**************************************************************************************/

	/*
		Function: SetType
		Set property Type

		Parameters:
			$type - 카테고리 구분
	*/
	public function SetType( $type ){
		$this->type = $type;
	}

	public function SetGoodsNoSub( $goods_no, $goods_sub ){
		$this->goods_no = $goods_no;
		$this->goods_sub = $goods_sub;
	}

	/*
		Function: __IsDuplicateGoods
		해당 카테고리에 해당 상품의 등록 여부 확인

		Returns:
			true - 기 등록 상태
			false - 미 등록 상태
	*/
	public function __IsDuplicateGoods(){
		$sql = "
			select count(*) cnt
			from category_goods
			where cat_type = '$this->type'
				and d_cat_cd = '$this->code'
				and goods_no = '$this->goods_no'
				and goods_sub = '$this->goods_sub'
		";
		$row = DB::selectOne($sql);
		$cnt = $row->cnt;
		if ( $cnt == 0) {
			return false;
		} else {
			return true;
		}
	}

	/*
		Function: __GetTopSeq
		해당 카테고리의 최상위 전시 순위

		Returns:
			$top_seq - 최상위 전시 순위
	*/
	public function __GetTopSeq(){
		$sql = "
			select (ifnull(min(seq),0) -1) as seq
			from category_goods
			where cat_type = '$this->type'
				and d_cat_cd = '$this->code'
		";
		$row = DB::selectOne($sql);
		$top_seq = $row->seq;
		return $top_seq;
	}

	/*
		Function: __GetBottomSeq
		해당 카테고리의 최하위 전시 순위

		Returns:
			$bottom_seq - 최하위 전시 순위
	*/
	function __GetBottomSeq(){
		$sql = "
			select ifnull(max(seq),0)+1 as seq
			from category_goods
			where cat_type = '$this->type'
				and d_cat_cd = '$this->code'
		";
		$row = DB::selectOne($sql);
		$bottom_seq = $row->seq;
		return $bottom_seq;
	}

	/*
		Function: AddGoods
		전시 상품을 카테고리에 추가
		- 카테고리 추가 시 중복 등록 여부 확인
		- 기본값은 카테고리의 최상위 출력

		See Also:
			- <__IsDuplicateGoods>
			- <__GetTopSeq>
			- <__GetBottomSeq>
	*/
	public function AddGoods($order = "", $disp_yn = "Y") {
		if (empty($this->type)) throw new Exception("AddGoods - type을 설정해야 합니다.", -1);
		if (empty($this->code)) throw new Exception("AddGoods - code를 설정해야 합니다.", -1);
		if (! isset($this->goods_no) || ! isset($this->goods_sub)) throw new Exception("AddGoods - goods_no와 goods_sub를 설정해야 합니다.", -1);

		$id = $this->user["id"];
		$name = $this->user["name"];

		if(! $this->__IsDuplicateGoods()) {
			if ($order == "99"){ // 최하위
				$seq = $this->__GetBottomSeq();
			}else { // 최상위
				$seq = $this->__GetTopSeq();
			}

			$sql = "
				insert into category_goods (
					cat_type, d_cat_cd, goods_no, goods_sub, seq, disp_yn, admin_id, admin_nm, regi_date
				) VALUES (
					'$this->type', '$this->code', '$this->goods_no', '$this->goods_sub', '$seq', '$disp_yn', '$id', '$name', now()
				)
			";
			DB::insert($sql);
		}

	}

	public function DeleteGoodsCode(){ // 해당 테이블의 특정 카테고리에 등록된 전시상품을 삭제
		if (empty($this->type)) throw new Exception("DeleteGoods - type을 설정해야 합니다.", -1);
		if (empty($this->code)) throw new Exception("DeleteGoods - code를 설정해야 합니다.", -1);
		if (! isset($this->goods_no) || ! isset($this->goods_sub)) throw new Exception("DeleteGoods - goods_no와 goods_sub를 설정해야 합니다.", -1);

		$sql = "
			delete from category_goods
			where cat_type = '$this->type'
				and d_cat_cd = '$this->code'
				and goods_no = '$this->goods_no'
				and goods_sub = '$this->goods_sub'
		";
		DB::delete($sql);
	}

	

}
