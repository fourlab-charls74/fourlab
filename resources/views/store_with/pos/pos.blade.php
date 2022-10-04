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
            <button type="button" onclick="return setScreen('pos_main');" class="butt butt-close bg-trans" style="width:55px;height:50px;border-left:1px solid #999"><i class="fa fa-home" aria-hidden="true"></i></button>
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
                <button type="button" class="butt w-100 fc-white fs-14 br-1 bg-orange mb-3" style="height: 60px;" data-toggle="modal" data-target="#searchProductModal"><i class="fa fa-search mr-2" aria-hidden="true"></i>상품 검색</button>
                <div class="d-flex mb-4">
                    <div class="table-responsive">
                        <div id="div-gd" class="ag-theme-balham" style="font-size: 18px;"></div>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="flex-1 mr-4">
                        <div class="d-flex justify-content-between align-items-center fs-15 fw-b mb-3">
                            <p>받을 금액</p>
                            <p><strong class="fc-red fs-20 fw-b mr-1">2,234,000</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb mb-2">
                            <p>결제한 금액</p>
                            <p><strong class="fw-b mr-1">0</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb">
                            <p>거스름돈</p>
                            <p><strong class="fc-red fw-b mr-1">0</strong>원</p>
                        </div>
                    </div>
                    <div class="flex-2 d-flex">
                        <button type="button" class="butt flex-1 fc-white fs-20 fw-sb br-2 bg-blue p-2 mr-3">신용카드</button>
                        <button type="button" class="butt flex-1 fc-white fs-20 fw-sb br-2 bg-blue p-2 mr-3">현금</button>
                        <button type="button" class="butt flex-1 fc-white fs-20 fw-sb br-2 bg-gray p-2">적립금사용</button>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-1 d-flex mr-4">
                        <button type="button" class="butt flex-1 fc-white fs-16 fw-sb br-1 bg-red p-4 mr-3" onclick="return setScreen('pos_main');">전체취소</button>
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
                <p class="fc-red">202209270959523139</p>
            </div>
            <div class="d-flex mb-4">
                <div class="flex-1 fw-sb b-gray p-4 mr-2" style="min-height:250px;">
                    {{-- <div class="d-flex justify-content-center align-items-center h-100 fc-gray fw-m">고객정보가 없습니다.</div> --}}
                    <p class="fs-18 fw-b mb-3">홍길동 <span class="fs-16 fw-sb">(남, 2000.01.01)</span></p>
                    <div class="d-flex align-items-center fs-12 mb-2">
                        <p style="min-width: 80px;">연락처</p>
                        <p>010-0000-0000</p>
                    </div>
                    <div class="d-flex align-items-center fs-12 mb-2">
                        <p style="min-width: 80px;">이메일</p>
                        <p>test@test.com</p>
                    </div>
                    <div class="d-flex align-items-center fs-12 mb-2">
                        <p style="min-width: 80px;">주소</p>
                        <p class="fs-10">경기도 성남시 판교역로 230 208-2호</p>
                    </div>
                    <div class="d-flex align-items-center fs-12">
                        <p style="min-width: 80px;">적립금</p>
                        <p class="fc-red fw-b">0</p>
                    </div>
                </div>
                <div class="d-flex flex-column">
                    <button type="button" class="butt flex-1 fc-white fs-14 fw-sb bg-gray p-4 mb-2">고객검색</button>
                    <button type="button" class="butt flex-1 fc-white fs-14 fw-sb bg-gray p-4">고객등록</button>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div class="d-flex b-gray mr-4" style="width:150px;height:150px;">
                    <img src="http://newera5950.jpg3.kr/item/12359429_1.jpg" alt="" class="w-100">
                </div>
                <div class="flex-1">
                    <ul class="fs-12 fw-sb">
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">상품명</p>
                            <p class="text-right">피엘라벤 우먼 아비스코 미드서머 자켓 Abisko Midsummer Jacket W (89826)</p>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">옵션명</p>
                            <p class="text-right">Dark Olive.Dark Olive^36 (27~28 inch)</p>
                        </li>
                        <li class="d-flex justify-content-between">
                            <p class="fc-blue fw-b" style="min-width: 80px;">상품코드</p>
                            <p class="text-right">F181WTR05CBOD36</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="d-flex">
                <div class="flex-1 b-gray mr-4">
                    <table class="prd_info_table w-100 fs-10" style="table-layout:fixed;">
                        <tr>
                            <th>수량</th>
                            <td>0</td>
                        </tr> 
                        <tr>
                            <th>단가</th>
                            <td>0</td>
                        </tr> 
                        <tr>
                            <th>소비자가</th>
                            <td>0</td>
                        </tr> 
                        <tr>
                            <th>판매유형</th>
                            <td>-</td>
                        </tr> 
                        <tr>
                            <th>행사명</th>
                            <td>-</td>
                        </tr> 
                        <tr>
                            <th>쿠폰</th>
                            <td>-</td>
                        </tr> 
                    </table>
                </div>
                <div class="flex-2 d-flex justify-content-end">
                    <div class="calculator-grid fs-20">
                        <input type="text" class="inp fc-black fs-20 fw-b text-right pr-3" style="grid-area:a;border:2px solid #bbb;">
                        <button type="button" class="butt bg-white" style="grid-area:b;">1</button>
                        <button type="button" class="butt bg-white" style="grid-area:c;">2</button>
                        <button type="button" class="butt bg-white" style="grid-area:d;">3</button>
                        <button type="button" class="butt bg-white" style="grid-area:e;">4</button>
                        <button type="button" class="butt bg-white" style="grid-area:f;">5</button>
                        <button type="button" class="butt bg-white" style="grid-area:g;">6</button>
                        <button type="button" class="butt bg-white" style="grid-area:h;">7</button>
                        <button type="button" class="butt bg-white" style="grid-area:i;">8</button>
                        <button type="button" class="butt bg-white" style="grid-area:j;">9</button>
                        <button type="button" class="butt bg-white" style="grid-area:k;">0</button>
                        <button type="button" class="butt bg-white" style="grid-area:l;">00</button>
                        <button type="button" class="butt bg-white" style="grid-area:m;">000</button>
                        <button type="button" class="butt fs-14 bg-lightgray" style="grid-area:n;">clear</button>
                        <button type="button" class="butt fs-14 bg-lightgray" style="grid-area:o;"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                        <button type="button" class="butt fs-14 fc-white bg-gray" style="grid-area:p;">수량변경</button>
                        <button type="button" class="butt fs-14 fc-white bg-gray" style="grid-area:q;">단가변경</button>
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
<div id="pos-modal">
    <div class="modal fade" id="searchProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{-- <h5 class="mt-1 fs-12 fw-b">영수증번호</h5>
                    <div class="d-flex flex-column align-items-center mt-5">
                        <input type="text" class="inp mb-4 p-2 w-75 text-center fs-18 fw-sb" value="M0001202207010001" />
                        <div class="d-flex justify-content-end w-100 fs-12">
                            <button type="button" class="btn p-2 pl-5 pr-5 mr-2 fc-gray fw-sb" data-dismiss="modal">취소</button>
                            <button type="button" class="btn p-2 pl-5 pr-5 text-light fw-sb bg-primary rounded-0">확인</button>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="modal fade" id="receiptNoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="mt-1 fs-12 fw-b">영수증번호</h5>
                    <div class="d-flex flex-column align-items-center mt-5">
                        <input type="text" class="inp mb-4 p-2 w-75 text-center fs-18 fw-sb" value="M0001202207010001" />
                        <div class="d-flex justify-content-end w-100 fs-12">
                            <button type="button" class="btn p-2 pl-5 pr-5 mr-2 fc-gray fw-sb" data-dismiss="modal">취소</button>
                            <button type="button" class="btn p-2 pl-5 pr-5 text-light fw-sb bg-primary rounded-0">확인</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

@include('store_with.pos.pos_js')

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {gridId:"#div-gd"});
	let gx;

    let AlignCenter = {"text-align": "center"};
    let LineHeight50 = {"line-height": "50px", "font-weight": "500"};
    const columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {...AlignCenter, ...LineHeight50}},
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
            // cellRenderer: (params) => `<a href="javascript:void(0);" onclick="setGoodsInfo('${params.data.prd_cd}');">${params.value}</a>`,
        },
        {field: "color", headerName: "컬러", width: 80, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "size", headerName: "사이즈", width: 80, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "qty", headerName: "수량", width: 80, type: "currencyType", cellStyle: LineHeight50},
        {field: "price", headerName: "단가", width: 100, type: "currencyType", cellStyle: LineHeight50},
        {field: "total", headerName: "금액", width: 120, type: "currencyType", cellStyle: {...LineHeight50, "font-size": "18px", "font-weight": "700"}},
    ];

	$(document).ready(function() {
		pApp.ResizeGrid(275, 560);
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);

        gx.gridOptions.api.setRowData([{
            prd_cd: "F182MSR03CBDG42",
            img: "http://newera5950.jpg3.kr/item/12359429_1.jpg",
            goods_nm: "피엘라벤 우먼 아비스코 미드서머 자켓 Abisko Midsummer Jacket W (89826)",
            color: "BB",
            size: "OS",
            qty: 1,
            price: 9999000,
            total: 9999000
        },{
            prd_cd: "F182MSR03CBDG42",
            img: "http://newera5950.jpg3.kr/item/12359429_1.jpg",
            goods_nm: "피엘라벤 우먼 아비스코 미드서머 자켓 Abisko Midsummer Jacket W (89826)",
            color: "BB",
            size: "OS",
            qty: 1,
            price: 9999000,
            total: 9999000
        },{
            prd_cd: "F182MSR03CBDG42",
            img: "http://newera5950.jpg3.kr/item/12359429_1.jpg",
            goods_nm: "피엘라벤 우먼 아비스코 미드서머 자켓 Abisko Midsummer Jacket W (89826)",
            color: "BB",
            size: "OS",
            qty: 1,
            price: 9999000,
            total: 9999000
        }]);
	});
</script>

<script>
    // function setGoodsInfo(prd_cd) {
    //     // prd_cd 로 상품검색하기 추후 추가예정
    //     // const test = {
    //     //     prd_cd: "F182MSR03CBDG42",
    //     //     img: "http://newera5950.jpg3.kr/item/12359429_1.jpg",
    //     //     goods_nm: "피엘라벤 우먼 아비스코 미드서머 자켓 Abisko Midsummer Jacket W (89826)",
    //     //     color: "BB",
    //     //     size: "OS",
    //     //     qty: 1,
    //     //     price: 9999000,
    //     //     total: 9999000
    //     // };

    //     $("#goods_box").removeClass("d-none").addClass("d-flex");
    //     $("#no_goods_box").removeClass("d-flex").addClass("d-none");
    // }
</script>

@stop