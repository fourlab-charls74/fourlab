@extends('store_with.layouts.layout-nav')
@section('title','POS')
@section('content')

<link href="{{ URL::asset('css/pos.css')}}" rel="stylesheet" type="text/css" />

<div id="pos" class="d-flex flex-column w-100 m-0" style="min-height: 100vh;max-width: 100vw;">
    {{-- 포스 헤더 --}}
    <div id="pos_header" class="d-flex justify-content-between align-items-center bg-white pl-3" style="border-bottom: 1px solid #999;">
        <h1 style="width:90px;"><img src="/theme/{{config('shop.theme')}}/images/pc_logo_white.png" alt="" class="w-100"></h1>
        <div class="d-flex align-items-center">
            <p class="fw-b mr-5">[L0025] 롯데본점</p>
            <p class="fw-sb mr-4">2022년 09월 28일 00:00:00</p>
            <button type="button" id="home_btn" onclick="return setScreen('pos_main');" class="butt butt-close bg-trans" style="width:55px;height:50px;border-left:1px solid #999"><i class="fa fa-home" aria-hidden="true"></i></button>
            <button type="button" onclick="return window.close();" class="butt butt-close bg-trans" style="width:55px;height:50px;border-left:1px solid #999"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
    </div>

    {{-- 메인화면 --}}
    <div id="pos_main" class="flex-1 d-flex justify-content-center align-items-center">
        <div class="main-grid fc-white">
            <button type="button" class="butt fs-20 fw-sb bg-orange" style="grid-area:a;" onclick="return setScreen('pos_order');">
                <i class="fa fa-shopping-bag d-block mb-3" aria-hidden="true" style="font-size:100px;"></i>
                주문등록
            </button>
            <div class="d-flex flex-column fc-white fs-12 bg-brown p-1" style="grid-area:b;">
                <p class="text-center fs-16 fw-sb p-4" style="border-bottom:2px solid #999;">매출분석</p>
                <ul class="p-3">
                    <li class="d-flex justify-content-between fw-sb mb-2"><p>총 매출금액</p><p class="fc-red"><span>0</span>원</p></li>
                    <li class="d-flex justify-content-between mb-2"><p>판매수량</p><p><span>0</span>개</p></li>
                    <li class="d-flex justify-content-between"><p>주문건수</p><p><span>0</span>건</p></li>
                </ul>
            </div>
            <button type="button" class="butt fs-14 fw-sb bg-blue" style="grid-area:c;" onclick="return setScreen('pos_today')">
                <i class="fa fa-search d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                당일판매내역
            </button>
            <button type="button" class="butt fs-14 fw-sb bg-gray" style="grid-area:d;">
                <i class="fa fa-plus-circle d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                부가기능
            </button>
            <div class="d-flex flex-column justify-content-between align-items-stretch align-items-center fs-12 bg-navy p-1" style="grid-area:e;">
                <div class="w-100">
                    <p class="text-center fs-16 fw-sb p-4" style="border-bottom:2px solid #999;">직전결제내역</p>
                    <ul class="p-3">
                        <li class="d-flex justify-content-between fw-sb mb-2"><p>총 결제금액</p><p class="fc-red"><span>0</span>원</p></li>
                        <li class="d-flex justify-content-between mb-2"><p>주문금액</p><p><span>0</span>개</p></li>
                        <li class="d-flex justify-content-between mb-2"><p>할인금액</p><p><span>0</span>건</p></li>
                        <li class="d-flex justify-content-between"><p>결제시간</p><p>00시 00분</p></li>
                    </ul>
                </div>
                <button type="button" class="butt fc-navy fw-b bg-white m-2" style="height:60px;border-radius:12px;">영수증 조회</button>
            </div>
            <button type="button" class="butt fs-14 fw-sb bg-mint" style="grid-area:f;">
                <i class="fa fa-bookmark d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                대기 1
            </button>
            <button type="button" class="butt fs-14 fw-sb bg-red" style="grid-area:g;">
                <i class="fa fa-reply d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                환불
            </button>
        </div>
    </div>

    {{-- 주문등록화면 --}}
    <div id="pos_order" class="flex-1 d-none">
        <div class="flex-5 p-3">
            <div class="d-flex flex-column">
                <button type="button" class="butt w-100 fc-white fs-14 br-1 bg-gray mb-3" style="height: 60px;" data-toggle="modal" data-target="#searchProductModal"><i class="fa fa-search mr-2" aria-hidden="true"></i>상품 검색</button>
                <div class="d-flex mb-4">
                    <div class="table-responsive">
                        <div id="div-gd" class="ag-theme-balham" style="font-size: 18px;"></div>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="flex-1 mr-4">
                        <div class="d-flex justify-content-between align-items-center fs-15 fw-b mb-3">
                            <p>총 주문금액</p>
                            <p><strong id="total_order_amt" class="fc-red fs-20 fw-b mr-1">0</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb mb-2">
                            <p>결제한 금액</p>
                            <p><strong id="payed_amt" class="fw-b mr-1">0</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb">
                            <p>거스름돈</p>
                            <p><strong id="change_amt" class="fc-red fw-b mr-1">0</strong>원</p>
                        </div>
                    </div>
                    <div class="flex-2 d-flex">
                        <button type="button" class="butt flex-2 fc-white fs-20 fw-b br-2 bg-blue p-2 mr-3" data-toggle="modal" data-target="#payModal" data-title="신용카드 결제" data-pay-type="card_amt">
                            <span id="card_amt">0</span>
                            <input type="hidden" name="card_amt" value="0">
                            <span class="d-block fs-14 fw-sb mt-1">신용카드</span>
                        </button>
                        <div class="flex-3 d-flex flex-column mr-3">
                            <button type="button" class="butt flex-1 fc-white fs-20 fw-b br-2 bg-blue p-2 mb-3" data-toggle="modal" data-target="#payModal" data-title="현금 결제" data-pay-type="cash_amt">
                                <span id="cash_amt">0</span>
                                <input type="hidden" name="cash_amt" value="0">
                                <span class="d-block fs-14 fw-sb mt-1">현금</span>
                            </button>
                            <button type="button" class="butt flex-1 fc-white fs-20 fw-b br-2 bg-gray p-2" data-toggle="modal" data-target="#payModal" data-title="적립금 사용" data-pay-type="point_amt">
                                <span id="point_amt">0</span>
                                <input type="hidden" name="point_amt" value="0">
                                <span class="d-block fs-14 fw-sb mt-1">적립금</span>
                            </button>
                        </div>
                        <button type="button" class="butt flex-2 fc-white fs-20 fw-b br-2 bg-mint p-2" onclick="return sale();">판매</button>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-1 d-flex mr-4">
                        <button type="button" class="butt flex-1 fc-white fs-16 fw-sb br-1 bg-red p-4 mr-3" onclick="return cancelOrder();">전체취소</button>
                        <button type="button" class="butt flex-1 fc-white fs-16 fw-sb br-1 bg-gray p-4">대기</button>
                    </div>
                    <div class="flex-2">
                        <textarea name="memo" id="memo" rows="2" class="w-100 h-100 fs-12 p-2 mr-2 noresize" placeholder="특이사항"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-3 p-3">
            <div class="d-flex justify-content-between fs-16 fw-sb mt-2 mb-3">
                <p>주문번호</p>
                <p id="ord_no" class="fc-red"></p>
            </div>
            <div class="d-flex mb-4">
                <div class="flex-1 position-relative fw-sb b-2-gray p-4 mr-2" style="min-height:250px;">
                    <div id="no_user" class="d-flex justify-content-center align-items-center h-100 fc-gray fw-m">고객정보가 없습니다.</div>
                    {{-- <div id="user">
                        <p class="fs-18 fw-b mb-3"><span id="user_nm">홍길동</span> <span id="user_info" class="fs-16 fw-sb">(남, 2000.01.01)</span> <span id="user_id" class="fs-14 fw-m">- gildong123</span></p>
                        <div class="d-flex align-items-center fs-12 mb-2">
                            <p style="min-width: 80px;">연락처</p>
                            <p id="user_phone"></p>
                        </div>
                        <div class="d-flex align-items-center fs-12 mb-2">
                            <p style="min-width: 80px;">이메일</p>
                            <p id="user_email"></p>
                        </div>
                        <div class="d-flex align-items-center fs-12 mb-2">
                            <p style="min-width: 80px;">주소</p>
                            <p id="user_address" class="fs-10"></p>
                        </div>
                        <div class="d-flex align-items-center fs-12">
                            <p style="min-width: 80px;">적립금</p>
                            <p id="user_point" class="fc-red fw-b">0</p>
                        </div>
                    </div> --}}
                    <div class="d-flex" style="position:absolute;bottom:12px;right:12px;">
                        <button type="button" onclick="return searchUser();" class="butt fc-white fs-10 fw-sb br-1 bg-gray pb-2 pt-2 pl-3 pr-3 mr-2">고객검색</button>
                        <button type="button" onclick="return addUser();" class="butt fc-white fs-10 fw-sb br-1 bg-blue pb-2 pt-2 pl-3 pr-3">고객등록</button>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div class="d-flex b-2-gray mr-4" style="width:150px;height:150px;">
                    <img src="" alt="" id="cur_img" class="w-100">
                </div>
                <div class="flex-1">
                    <ul class="fs-12 fw-sb">
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">상품명</p>
                            <p class="text-right" id="cur_goods_nm"></p>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">옵션명</p>
                            <p class="text-right" id="cur_goods_opt"></p>
                        </li>
                        <li class="d-flex justify-content-between">
                            <p class="fc-blue fw-b" style="min-width: 80px;">상품코드</p>
                            <p class="text-right" id="cur_prd_cd"></p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="d-flex">
                <div class="flex-1 b-2-gray mr-4">
                    <table class="prd_info_table w-100 fs-10">
                        <tr>
                            <th>수량</th>
                            <td id="cur_qty" class="pr-3">-</td>
                        </tr> 
                        <tr>
                            <th>단가</th>
                            <td id="cur_price" class="pr-3">-</td>
                        </tr> 
                        <tr>
                            <th>TAG가</th>
                            <td id="cur_goods_sh" class="pr-3">-</td>
                        </tr> 
                        <tr>
                            <th>판매유형</th>
                            <td>
                                <div class="d-flex pl-3 pr-1">
                                    <select name="sale_type" id="sale_type" class="sel w-100" onchange="return updateOrderValue('sale_type', event.target.value);"></select>
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <th>행사명</th>
                            <td>
                                <div class="d-flex pl-3 pr-1">
                                    <select name="pr_code" id="pr_code" class="sel w-100" onchange="return updateOrderValue('pr_code', event.target.value)">
                                        @foreach (@$pr_codes as $pr_code)
                                            <option value="{{ $pr_code->pr_code }}">{{ $pr_code->pr_code_nm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <th>쿠폰</th>
                            <td class="pr-3">-</td>
                        </tr> 
                    </table>
                </div>
                <div class="flex-2 d-flex justify-content-end">
                    <div id="product_calculator" class="calculator-grid product fs-20">
                        <input type="text" id="product_press_amt" class="inp fc-black fs-20 fw-b text-right pr-3" style="grid-area:a;border:2px solid #bbb;" value="0">
                        <button type="button" class="butt bg-white" value="1" style="grid-area:b;">1</button>
                        <button type="button" class="butt bg-white" value="2" style="grid-area:c;">2</button>
                        <button type="button" class="butt bg-white" value="3" style="grid-area:d;">3</button>
                        <button type="button" class="butt bg-white" value="4" style="grid-area:e;">4</button>
                        <button type="button" class="butt bg-white" value="5" style="grid-area:f;">5</button>
                        <button type="button" class="butt bg-white" value="6" style="grid-area:g;">6</button>
                        <button type="button" class="butt bg-white" value="7" style="grid-area:h;">7</button>
                        <button type="button" class="butt bg-white" value="8" style="grid-area:i;">8</button>
                        <button type="button" class="butt bg-white" value="9" style="grid-area:j;">9</button>
                        <button type="button" class="butt bg-white" value="0" style="grid-area:k;">0</button>
                        <button type="button" class="butt bg-white" value="00" style="grid-area:l;">00</button>
                        <button type="button" class="butt bg-white" value="000" style="grid-area:m;">000</button>
                        <button type="button" class="butt fs-14 bg-lightgray" value="removeAll" style="grid-area:n;">clear</button>
                        <button type="button" class="butt fs-14 bg-lightgray" value="remove" style="grid-area:o;"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                        <button type="button" class="butt fs-14 fc-white bg-gray" value="qty" style="grid-area:p;">수량변경</button>
                        <button type="button" class="butt fs-14 fc-white bg-gray" value="price" style="grid-area:q;">단가변경</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 당일판매내역화면 --}}
    <div id="pos_today" class="flex-1 d-none align-items-center justify-content-center">
        당일판매내역
    </div>
</div>

{{-- MODAL --}}
<div id="pos-modal" class="show_layout">
    {{-- 상품검색모달 --}}
    <div class="modal fade" id="searchProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">상품 검색</h5>
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex align-items-center br-2 b-1-gray bg-white shadow-box p-2 pl-4 mb-3">
                                <select name="search_prd_type" id="search_prd_type" class="sel fs-12" style="min-width: 120px;">
                                    <option value="prd_cd">상품코드</option>
                                    <option value="goods_nm">상품명</option>
                                </select>
                                <input type="text" class="flex-1 inp h-40 fs-12 mr-1" id="search_prd_keyword" name="search_prd_keyword" placeholder="검색어를 입력하세요">
                                <button type="button" class="butt br-2 bg-lightgray p-3" onclick="return Search();"><i class="fa fa-search fc-black fs-10" aria-hidden="true"></i></button>
                            </div>
                            <div class="d-flex">
                                <div class="table-responsive">
                                    <div id="div-gd-product" class="ag-theme-balham" style="font-size: 18px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 신용카드모달 --}}
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 id="paymodal_title" class="mt-1 fs-14 fw-b"></h5>
                            <input type="hidden" name="paymodal_paytype">
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex flex-column align-items-center">
                                <div class="d-flex justify-content-between align-items-center fs-15 fw-b w-100 mb-3">
                                    <p>남은 금액</p>
                                    <p class="butt curson-pointer" onclick="return setDueAmt();"><strong id="due_amt" class="fc-red fs-20 fw-b mr-1">0</strong>원</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-12 fw-sb w-100">
                                    <p>총 주문금액</p>
                                    <p><strong id="total_order_amt2" class="fw-b mr-1">0</strong>원</p>
                                </div>
                                <div id="payment_calculator" class="calculator-grid payment fs-20 mt-4">
                                    <input type="text" id="pay_press_amt" class="inp fc-black fs-20 fw-b text-right pr-3" style="grid-area:a;border:2px solid #bbb;" value="0">
                                    <button type="button" class="butt bg-white" value="1" style="grid-area:b;">1</button>
                                    <button type="button" class="butt bg-white" value="2" style="grid-area:c;">2</button>
                                    <button type="button" class="butt bg-white" value="3" style="grid-area:d;">3</button>
                                    <button type="button" class="butt bg-white" value="4" style="grid-area:e;">4</button>
                                    <button type="button" class="butt bg-white" value="5" style="grid-area:f;">5</button>
                                    <button type="button" class="butt bg-white" value="6" style="grid-area:g;">6</button>
                                    <button type="button" class="butt bg-white" value="7" style="grid-area:h;">7</button>
                                    <button type="button" class="butt bg-white" value="8" style="grid-area:i;">8</button>
                                    <button type="button" class="butt bg-white" value="9" style="grid-area:j;">9</button>
                                    <button type="button" class="butt bg-white" value="0" style="grid-area:k;">0</button>
                                    <button type="button" class="butt bg-white" value="00" style="grid-area:l;">00</button>
                                    <button type="button" class="butt fs-14 bg-white" value="remove" style="grid-area:m;"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                                    <button type="button" class="butt fs-14 bg-lightgray" value="removeAll" style="grid-area:n;">clear</button>
                                    <button type="button" class="butt fs-18 fc-white bg-blue" value="active" style="grid-area:o;">적용</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {gridId: "#div-gd"});
	let gx;

    let AlignCenter = {"text-align": "center"};
    let LineHeight50 = {"line-height": "50px"};
    const columns = [
        // {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "prd_cd", hide: true},
        // {field: "img", headerName: "이미지", width: 50, cellStyle: {...AlignCenter, ...LineHeight50},
        //     cellRenderer: (params) => {
        //         return `
        //             <div class="d-flex justify-content-center align-items-center" style="width:50px;height:50px;overflow:hidden;">
        //                 <img src="${params.value}" alt="${params.data.goods_nm}" class="w-100">
        //             </div>
        //         `;
        //     }
        // },
        {field: "goods_nm", headerName: "상품명", width: "auto", cellStyle: LineHeight50, wrapText: true, autoHeight: true,
            // cellRenderer: (params) => `<a href="javascript:void(0);" onclick="setProductDetail('${params.data.prd_cd}');">${params.value}</a>`,
        },
        {field: "color", headerName: "컬러", width: 80, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "size", headerName: "사이즈", width: 80, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "qty", headerName: "수량", width: 80, type: "currencyType", cellStyle: LineHeight50},
        {field: "price", headerName: "단가", width: 100, type: "currencyType", cellStyle: LineHeight50},
        {field: "total", headerName: "금액", width: 120, type: "currencyType", cellStyle: {...LineHeight50, "font-size": "18px", "font-weight": "700"}},
        {headerName: "삭제", width: 80, cellStyle: {...AlignCenter, ...LineHeight50},
            cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return removeProduct('${params.data.prd_cd}')"><i class="fa fa-trash fc-red fs-12" aria-hidden="true"></i></a>`,
        }
    ];

    const pApp2 = new App('', {gridId: "#div-gd-product"});
    let gx2;

    const product_columns = [
        {field: "prd_cd" , headerName: "바코드", width: 180, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "prd_cd_sm", headerName: "상품코드", width: 130, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "color", headerName: "컬러", width: 80, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "size", headerName: "사이즈", width: 80, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "goods_nm",	headerName: "상품명", width: "auto", cellStyle: LineHeight50,
            cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return addProduct('${params.data.prd_cd}')">${params.value}</a>`,
        },
        {field: "goods_opt", headerName: "옵션", width: 300, cellStyle: LineHeight50},
        {field: "wqty", headerName: "매장수량", type: "currencyType", width: 100, cellStyle: LineHeight50},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 100, cellStyle: LineHeight50},
        {field: "price", headerName: "판매가", type: "currencyType", width: 100, cellStyle: LineHeight50},
    ];

    const sale_types = <?= json_encode(@$sale_types) ?>; // 판매유형
    const pr_codes = <?= json_encode(@$pr_codes) ?>; // 행사명

	$(document).ready(function() {
		pApp.ResizeGrid(275, 470);
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
            rowSelection: 'single',
            suppressRowClickSelection: false,
            onSelectionChanged: function(e) {
                let goods = e.api.getSelectedRows();
                if(goods.length > 0) {
                    setProductDetail(goods[0].prd_cd);
                } else {
                    setProductDetail();
                }
                updateOrderValue();
            }
        });

		pApp2.ResizeGrid(275, 400);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
		let gridDiv2 = document.querySelector(pApp2.options.gridId);
		gx2 = new HDGrid(gridDiv2, product_columns);


        // ELEMENT EVENT
        $("#search_prd_keyword").on("keypress", function (e) {
            if(e.keyCode === 13) Search();
        });
        $('#searchProductModal').on('shown.bs.modal', function () {
            $('#search_prd_keyword').trigger('focus');
        });
        $("#product_calculator").on({
            click: function({target}) {
                if(target.nodeName == "BUTTON") {
                    let str = $("#product_press_amt").val().replaceAll(",", "");
                    switch (target.value) {
                        case 'remove':
                            str = str.slice(0, str.length - 1); 
                            break;
                        case 'removeAll':
                            str = ''; 
                            break;
                        case 'qty':
                            updateOrderValue('cur_qty', str * 1);
                            str = ''; 
                            break;
                        case 'price':
                            updateOrderValue('cur_price', str * 1);
                            str = ''; 
                            break;
                        default:
                            str += target.value;
                            break;
                    }
                    $("#product_press_amt").val(isNaN(str * 1) ? 0 : Comma(str * 1));
                }
            },
            keyup: function(e) {
                if((e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode == 8 || (e.keyCode >= 37 && e.keyCode <= 40)) {
                    let num = unComma(e.target.value);
                    e.target.value = Comma(isNaN(num) ? 0 : num);
                }
            }
        });
        $('#payModal').on('show.bs.modal', function(e) {
            let title = $(e.relatedTarget).data('title');
            let paytype = $(e.relatedTarget).data('pay-type');
            $(e.currentTarget).find('#paymodal_title').text(title);
            $(e.currentTarget).find('[name=paymodal_paytype]').val(paytype);
        });
        $('#payModal').on('hide.bs.modal', function(e) {
            $("#pay_press_amt").val(0);
        });
        $("#payment_calculator").on({
            click: function(e) {
                if(e.target.nodeName == "BUTTON") {
                    let str = $("#pay_press_amt").val().replaceAll(",", "");
                    switch (e.target.value) {
                        case 'remove':
                            str = str.slice(0, str.length - 1); 
                            break;
                        case 'removeAll':
                            str = ''; 
                            break;
                        case 'active':
                            let paytype = $('[name=paymodal_paytype]').val();
                            updateOrderValue(paytype, str * 1);
                            $('#payModal').modal('hide');
                            str = ''; 
                            break;
                        default:
                            str += e.target.value;
                            break;
                    }
                    $("#pay_press_amt").val(isNaN(str * 1) ? 0 : Comma(str * 1));
                }
            },
            keyup: function(e) {
                if((e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode == 8 || (e.keyCode >= 37 && e.keyCode <= 40)) {
                    let num = unComma(e.target.value);
                    e.target.value = Comma(isNaN(num) ? 0 : num);
                } else if(e.keyCode == 13) {
                    updateOrderValue('card_amt', str * 1);
                    $('#payModal').modal('hide');
                }
            }
        });
	});
</script>

@include('store_with.pos.pos_js')

@stop