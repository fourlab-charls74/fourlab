@extends('head_with.layouts.layout-nav')
@section('title','상품 연동 조건 설정')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">크리테오</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 제휴</span>
                <span>/ 크리테오</span>
                <span>/ 설정</span>
            </div>
        </div>
    </div>
    <!-- <전체 상품> -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#전체상품">전체상품</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#설정">설정</a>
        </li>
    </ul>
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="전체상품">
                    <div class="card shadow">
                        <div class="card-header mb-0">
                            <a href="#">전체상품 연동설정</a>
                        </div>
                        <div class="card-body mt-1">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-box-ty2 mobile">
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                <colgroup>
                                                    <col width="150px">
                                                </colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th>대표 카테고리</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_d_cat_type_all" id="ex_d_cat_type_all_0" class="custom-control-input" value="0" @if(@$shop_ad_goods_all->ex_d_cat_type == '0') checked @endif />
                                                                    <label class="custom-control-label" for="ex_d_cat_type_all_0">전체 카테고리 노출</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_d_cat_type_all" id="ex_d_cat_type_all_1" class="custom-control-input" value="1" @if(@$shop_ad_goods_all->ex_d_cat_type == '1') checked @endif />
                                                                    <label class="custom-control-label" for="ex_d_cat_type_all_1">일부 카테고리 제외</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md add-groups ex_d_cat_type_all <?=$shop_ad_goods_all->ex_d_cat_type == '1' ? null : 'd-none' ?>">
                                                                <select name="ex_d_cat_cd" multiple class="form-control form-control-sm" style="height: 100px">
                                                                    <option value="">== 상품을 추가해 주십시오. ==</option>
                                                                    <?php
                                                                        foreach ($all_except_some['except_rep_category'] as $key => $value) {
                                                                            echo "<option value='${key}'>${value}</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <div class="mt-2">
                                                                    <a href="#" onclick="addGroup('ex_d_cat_cd')" class="btn btn-sm btn-secondary">추가</a>
                                                                    <a href="#" onclick="delGroup('ex_d_cat_cd')" class="btn btn-sm btn-secondary add-group-btn">삭제</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>업체</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_com_type_all" id="ex_com_type_all_0" class="custom-control-input" value="0" @if(@$shop_ad_goods_all->ex_com_type == '0') checked @endif />
                                                                    <label class="custom-control-label" for="ex_com_type_all_0">전체 업체 노출</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_com_type_all" id="ex_com_type_all_1" class="custom-control-input" value="1" @if(@$shop_ad_goods_all->ex_com_type == '1') checked @endif />
                                                                    <label class="custom-control-label" for="ex_com_type_all_1">일부 업체 제외</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md add-groups ex_com_type_all <?=$shop_ad_goods_all->ex_com_type == '1' ? null : 'd-none' ?>">
                                                                <select name="ex_com_id" multiple class="form-control form-control-sm" style="height: 100px">
                                                                    <option value="">== 업체를 추가해 주십시오. ==</option>
                                                                    <?php
                                                                        foreach ($all_except_some['except_company'] as $key => $value) {
                                                                            echo "<option value='${key}'>${value}</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <div class="mt-2">
                                                                    <a href="#" onclick="addGroup('ex_com_id')" class="btn btn-sm btn-secondary">추가</a>
                                                                    <a href="#" onclick="delGroup('ex_com_id')" class="btn btn-sm btn-secondary add-group-btn">삭제</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>브랜드</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_brand_type_all" id="ex_brand_type_all_0" class="custom-control-input" value="0" @if(@$shop_ad_goods_all->ex_brand_type == '0') checked @endif />
                                                                    <label class="custom-control-label" for="ex_brand_type_all_0">전체 브랜드 노출</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_brand_type_all" id="ex_brand_type_all_1" class="custom-control-input" value="1" @if(@$shop_ad_goods_all->ex_brand_type == '1') checked @endif />
                                                                    <label class="custom-control-label" for="ex_brand_type_all_1">일부 브랜드 제외</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md add-groups ex_brand_type_all <?=$shop_ad_goods_all->ex_brand_type == '1' ? null : 'd-none' ?>">
                                                                <select name="ex_brand" multiple class="form-control form-control-sm" style="height: 100px">
                                                                    <option value="">== 브랜드를 추가해 주십시오. ==</option>
                                                                    <?php
                                                                        foreach ($all_except_some['except_brand'] as $key => $value) {
                                                                            echo "<option value='${key}'>${value}</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <div class="mt-2">
                                                                    <a href="#" onclick="addGroup('ex_brand')" class="btn btn-sm btn-secondary">추가</a>
                                                                    <a href="#" onclick="delGroup('ex_brand')" class="btn btn-sm btn-secondary add-group-btn">삭제</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>상품</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_goods_type_all" id="ex_goods_type_all_0" class="custom-control-input" value="0" @if(@$shop_ad_goods_all->ex_goods_type == '0') checked @endif />
                                                                    <label class="custom-control-label" for="ex_goods_type_all_0">전체 상품 노출</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="ex_goods_type_all" id="ex_goods_type_all_1" class="custom-control-input" value="1" @if(@$shop_ad_goods_all->ex_goods_type == '1') checked @endif />
                                                                    <label class="custom-control-label" for="ex_goods_type_all_1">일부 상품 제외</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md add-groups ex_goods_type_all <?=$shop_ad_goods_all->ex_goods_type == '1' ? null : 'd-none' ?>">
                                                                <select name="ex_goods" multiple class="form-control form-control-sm" style="height: 100px">
                                                                    <option value="">== 상품을 추가해 주십시오. ==</option>
                                                                    <?php
                                                                        foreach ($all_except_some['except_goods'] as $key => $value) {
                                                                            echo "<option value='${key}'>${value}</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <div class="mt-2">
                                                                    <a href="#" onclick="addGroup('ex_goods')" class="btn btn-sm btn-secondary">추가</a>
                                                                    <a href="#" onclick="delGroup('ex_goods')" class="btn btn-sm btn-secondary add-group-btn">삭제</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>가격</th>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name='price_from_all' value="{{ number_format(@$shop_ad_goods_all->price_from) }}" />
                                                                        원 이상
                                                                    </div>
                                                                </div>
                                                                <span class="text_line">~</span>
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name='price_to_all' value="{{ number_format(@$shop_ad_goods_all->price_to) }}" />
                                                                        원 이하인 경우만 노출
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>재고수량</th>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name='qty_limit_all' value="{{ number_format(@$shop_ad_goods_all->qty_limit) }}" />
                                                                        개 이상일 경우만 노출
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="설정">
                    <div class="card shadow">
                        <div class="card-header mb-0">
                            <a href="#">설정</a>
                        </div>
                        <div class="card-body mt-1">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-box-ty2 mobile">
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                <colgroup>
                                                    <col width="94px">
                                                </colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th>사용여부</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="use_yn_set" id="use_set_y" class="custom-control-input" value="Y" @if(@$shop_ad_goods_set->use_yn == 'Y') checked @endif />
                                                                    <label class="custom-control-label" for="use_set_y">사용</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" name="use_yn_set" id="use_set_n" class="custom-control-input" value="N" @if(@$shop_ad_goods_set->use_yn == 'N') checked @endif />
                                                                    <label class="custom-control-label" for="use_set_n">사용 안함</label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>도메인</th>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name='domain_set' value="{{ (@$shop_ad_goods_set->domain) }}">
                                                                        예) www.bizest.co.kr
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>무이자할부</th>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name="card_desc_set" value="{{ (@$shop_ad_goods_set->card_desc) }}">
                                                                        예) 삼성3/현대6/국민12
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>머리말</th>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name="head_desc_set" value="{{ (@$shop_ad_goods_set->head_desc) }}">
                                                                        예) 브랜드 {BRAND},머리말 {HEAD_DESC}, 스타일넘버 {STYLE_NO}
                                                                    </div>
                                                                </div>
                                                                <a href="#" onclick="openAddHeadTail('head');" class="ml-2 btn btn-sm btn-secondary">추가</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>꼬리말</th>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-inline-inner input_box">
                                                                    <div class="form-group">
                                                                        <input type='text' class="form-control form-control-sm" name="tail_desc_set" value="{{ (@$shop_ad_goods_set->tail_desc) }}">
                                                                        예) 브랜드 {BRAND},머리말 {HEAD_DESC}, 스타일넘버 {STYLE_NO}
                                                                    </div>
                                                                </div>
                                                                <a href="#" onclick="openAddHeadTail('tail');" class="ml-2 btn btn-sm btn-secondary">추가</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>광고</th>
                                                        <td>
                                                            <div class="flex_box">
                                                                <?php
                                                                    $ads_list = $ads['list'];
                                                                    $selected_ad = $ads['selected'];
                                                                ?>
                                                                <select name="ad_set" id="ad_set" class="form-control form-control-sm">
                                                                    @foreach($ads_list as $item)
                                                                        @if(isset($selected_ad->value) && $item->value == $selected_ad->value)
                                                                            <option value='{{$item->value}}' selected>{{$item->name}}</option>
                                                                        @else
                                                                            <option value='{{$item->value}}'>{{$item->name}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bd" rowspan="1" colspan="3">
                                                            머리말과 꼬리말에 [비제스트] 라는 값을 넣으려면
                                                            <b>{[비제스트]}</b>
                                                            와 같은 형식으로 넣으시면 됩니다.
                                                            <br>
                                                            <br>
                                                            상품 DB URL
                                                            <br>
                                                            전체 상품
                                                            <a onclick="previewList('all')" href="#">[미리보기]</a>
                                                            <br>
                                                            {{ $goods_all_url }}
                                                            <br>
                                                            <br>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
    <div class="resul_btn_wrap mt-3 d-block">
        <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
        <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
    </div>
</div>
<script type="text/javascript" charset="utf-8">

    let code = '{{ $code  }}';

    const URL = {
        GOODS_ALL_URL: '{{ $goods_all_url }}'
    };

    document.addEventListener("DOMContentLoaded", () => {
        initRadioEvents();
    });

    /**
     * @return {boolean}
     */
    const Save = () => {

        if (!confirm('저장하시겠습니까?')) return false;

        var frm = $('form');
        const selects_query_string = allExceptSomeSave();

        $.ajax({
            method: 'post',
            url: '/head/product/prd22/' + code,
            data: frm.serialize() + selects_query_string,
            dataType: 'json',
            success: function(res) {
                // console.log(res);
                if (res.code == '200') {
                    alert("정상적으로 변경 되었습니다.");
                    window.close();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    console.log(res.msg);
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });

        return true;
    };

    const allExceptSomeSave = () => {
        const multi_selects = document.detail.querySelectorAll("#전체상품 select[multiple]");
        let selects_query_string = "";
        for (let i=0; i<multi_selects.length; i++) {
            const multi_select = multi_selects[i];
            const name = multi_select.name;
            selects_query_string += `&${name}=`;
            for (let j=1; j<multi_select.length; j++) {
                const option = multi_select[j];
                const delimiter = '\t';
                selects_query_string += ( (j>1) ? delimiter : "" ) + option.value;
            }
        }
        return selects_query_string;
    }

    const initRadioEvents = () => {
        const radios = document.detail.querySelectorAll("#전체상품 input[type='radio']");
        const multi_selects = document.detail.querySelectorAll("#전체상품 select[multiple]");
        for (let i=0; i<radios.length; i++) {
            radios[i].addEventListener('change', (event) => {
                const name = radios[i].name;
                const show = document.detail.querySelector(`#전체상품 .add-groups.${name}`);
                event.target.value == 1 ? show.classList.remove('d-none') : show.classList.add('d-none');
            })
        }
    }

    const commonAlert = () => { alert("이미 선택하신 항목입니다."); };

    const addGroup = (name) => { // head_search.js 프로토타입 전역함수와 goods_api 사용
        event.preventDefault();
        const group = document.querySelector(`#전체상품 select[name='${name}']`);
        const group_name = name;
        switch (group_name) {
            case 'ex_d_cat_cd': // 제외할 대표 카테고리 추가
                searchCategory.Open('DISPLAY', (code, name, full_name) => {
                    if (searchCategory.type === "ITEM") return alert("대표 카테고리는 전시 카테고리만 설정가능합니다.");
                    if (checkDuplicatedInSelect(code, group)) { commonAlert(); return false };
                    createOptionElement(group_name, code, full_name);
                });
                break;
            case 'ex_com_id': // 제외할 업체 조회
                searchCompany.Open((com_cd, com_nm, com_type, baesong_kind, baesong_info, margin_type, dlv_amt) => {
                    if (checkDuplicatedInSelect(com_cd, group)) { commonAlert(); return false };
                    createOptionElement(group_name, com_cd, com_nm);
                });
                break;
            case 'ex_brand': // 제외할 브랜드 추가
                searchBrand.Open((code, name) => {
                    if (checkDuplicatedInSelect(code, group)) { commonAlert(); return false };
                    createOptionElement(group_name, code, name);
                });
                // 브랜드 검색
                break;
            case 'ex_goods': // 제외할 상품 추가
                getSearchGoods(); // goods api
            default:
                break;
        }
    };

    const delGroup = (name) => {
        const obj = document.detail[name];
        for (var i = obj.options.length - 1; i >= 1; i--) {
		    if (obj.options[i].selected) {
			    obj.options[i] = null;
		    }
	    }
        obj.focus();
    };

    const previewList = (type) =>{ // 봇이 수집하는 파일 미리보기
        var url = "";
        if (type == "all") {
            url = URL.GOODS_ALL_URL;
        } else if (type == "new") {
            url = URL.GOODS_NEW_URL;
        }
        const [ top, left, width, height ] = [ 100, 100, 800, 700 ];
        window.open(url,"_blank",`toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=${top},left=${left},width=${width},height=${height}`);
    };

    const checkDuplicatedInSelect = (value, obj) => {
        for (var i = 1; i< obj.length; i++) {
		    if ( value == obj[i].value) return true;
	    }
	    return false;
    };

    const createOptionElement = (name, value, inner_html) => {
        const select = document.querySelector(`select[name=${name}]`);
        let opt = document.createElement('option');
        opt.value = value;
        opt.innerHTML = inner_html;
        select.appendChild(opt);
        select.focus();
    };

    /**
     * addGroups에서 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다. ( head_search.js searchGoods 프로토타입 에러나서 goods api로 처리 )
     */

    var addRow = (row) => {
        const { goods_no, goods_sub, goods_nm } = row;
        const group_name = 'ex_goods';
        const group = document.querySelector(`#전체상품 select[name='${group_name}']`);
        const goods_num = `${goods_no}|${goods_sub}`;
        if (checkDuplicatedInSelect(goods_num, group)) return false;
        createOptionElement(group_name, goods_num, goods_nm);
    };

    var goodsCallback = (row) => addRow(row);

    var multiGoodsCallback = (rows) => { if (rows && Array.isArray(rows)) rows.map(row => addRow(row)); };

    const getSearchGoods = () => {
        const url=`/head/api/goods/show`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    };

    /**
     * 머리말, 꼬리말 추가 - words api - head and tail
     */

    const openAddHeadTail = (type) => {
        event.preventDefault()
        let url = `/head/api/head_tail?type=${type}`;
        const [ top, left, width, height ] = [ 100, 700, 500, 700 ];
        window.open(url,"_blank",`toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=${top},left=${left},width=${width},height=${height}`);
    };

    var multiWordsCallback = (rows, type) => {
        let input_name = "";
        switch (type) {
            case "head":
                input_name = "head_desc_set";
                break;
            case "tail":
                input_name = "tail_desc_set";
                break;
            default:
                return;
        }
        const input = document.detail[input_name];
        const words_arr = input.value.split(',');
        if (rows && Array.isArray(rows)) rows.map(row => {
            let word = (input.value == "") ? `{${row.code_id}}` : `,{${row.code_id}}`;
            words_arr.includes(`{${row.code_id}}`) ? null : input.value += word;
        });
    };

</script>
@stop