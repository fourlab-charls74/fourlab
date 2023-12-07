@extends('store_with.layouts.layout-nav')
@section('title','상품입고관리')
@section('content')

<style>
    #help ul li {list-style: disc;list-style-position: inside;text-indent: -20px;padding-left: 20px;font-size: 13px;font-weight: 400;}
    #help p {font-size: 14px;font-weight: 700;padding-bottom: 5px;}
    .form-control[readonly] {background: #eeeeee;}
</style>

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">상품입고 {{ @$invoice_no ? (" - " . $invoice_no) : "" }}</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품관리</span>
            <span>/ 상품입고관리</span>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <input type="hidden" name="cmd" value="{{ @$cmd }}">
            <input type="hidden" name='stock_no' value='{{ @$stock_no }}'>
            <div class="card mb-3" id="input-area">
                <div class="card-header d-flex justify-content-between">
                    <h4>기본 정보</h4>
                    <div>
                        @if (@$super_admin == 'true' || (@$state > 0 && @$state < 40))
                            @if(Auth('head')->user()->logistics_group_yn == 'Y')
                                @if(@$state != 30)
                                    <a href="javascript:void(0);" onclick="cmder('{{ @$cmd }}')" class="btn btn-sm btn-primary shadow-sm"><i class="bx bx-save mr-1"></i>저장</a>
                                @endif
                            @else
                                <a href="javascript:void(0);" onclick="cmder('{{ @$cmd }}')" class="btn btn-sm btn-primary shadow-sm"><i class="bx bx-save mr-1"></i>저장</a>   
                            @endif
                            @if (@$stock_no != "" && @$state < 30)
                            <a href="javascript:void(0);" onclick="cmder('delcmd')" class="btn btn-sm btn-primary shadow-sm">입고삭제</a>
                            @endif
                            @if ($state == 30)
                                @if(Auth('head')->user()->logistics_group_yn == 'N')
                                <a href="javascript:void(0);" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm">추가입고</a>
                                <a href="javascript:void(0);" onclick="cmder('cancelcmd')" class="btn btn-sm btn-primary shadow-sm">입고취소</a>
                                @endif
							@elseif (@$super_admin === 'true')
	                            <a href="javascript:void(0);" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm">추가입고</a>
                            @endif
                        @endif
                        <a href="javascript:void(0);" onclick="return gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                        <a href="javascript:void(0);" onclick="return displayHelp();" class="btn btn-sm btn-outline-primary shadow-sm">도움말</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="com_nm" class="required">공급업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-75 pr-1">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-sup-company" value="{{ @$com_nm }}">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-inline-inner input-box w-25 pl-1">
                                        <input type="text" id="com_id" name="com_id" class="form-control form-control-sm" value="{{ @$com_id }}" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="invoice_no" class="required">입고번호</label>
                                <div class="flex_box">
                                    <input type="text" onfocus="return getInvoiceNo();" class="form-control form-control-sm" name="invoice_no" value="{{ @$invoice_no }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="bl_no">인보이스번호</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm" name="bl_no" value="{{ @$bl_no }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="formrow-firstname-input" class="required">입고일자</label>
                                <div class="flex_box">
                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" id="stock_date" 
                                                name="stock_date" value="{{ @$stock_date }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="" class="required">입고상태</label>
                                <div class="flex_box">
                                    <select name="state" id="state" class="form-control form-control-sm w-100">
                                        @foreach (@$states as $stt)
                                            <option value="{{ $stt['code_id'] }}" {{ $state == $stt['code_id'] ? 'selected' : '' }}>{{ $stt['code_val'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="loc">위치</label>
                                <div class="flex_box">
                                    <select name="loc" id="loc" class="form-control form-control-sm w-100" hidden>
                                        @foreach ($locs as $item)
                                            <option value="{{ $item->code_id }}" {{ $item->code_id == $loc ? 'selected' : '' }}>{{ $item->code_val }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control form-control-sm" name="loc" value="기본" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="f_sqty" class="required">환율</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box col col-6 col-sm-4 mx-auto p-0 pr-2">
                                        <select id="currency_unit" name="currency_unit" class="form-control form-control-sm w-100" onchange="return changeUnit(this);">
                                            <?php
                                                $currencies = ['KRW', 'USD', 'EUR', 'JPY', 'CNY', 'HKD'];
                                                collect($currencies)->map(function ($currency) use ($currency_unit) {
                                                    $selected = ($currency == $currency_unit) ? "selected" : "";
                                                    echo "<option value='${currency}' $selected>${currency}</option>";
                                                });
                                            ?>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center form-inline-inner input-box col col-6 col-sm-8 mx-auto p-0">
                                        <input type='text' class="form-control form-control-sm text-right w-100"
                                            name='exchange_rate' id='exchange_rate' value="{{ @$exchange_rate ?? 0 }}"
                                            onkeypress="checkFloat(event);" onkeyup="com3(this);calCustomTaxRate();" onfocus="this.select()" 
                                            {{ @$currency_unit == 'KRW' ? 'readonly disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="tariff_amt" class="required">관세총액/관세율</label>
                                <div class="flex_box align-items-start">
                                    <div class="col col-6 p-0 pr-2">
                                        <input type="text" class="form-control form-control-sm text-right" 
                                            id="tariff_amt" name="tariff_amt" value="{{ number_format(@$tariff_amt ?? 0) }}"
                                            onkeypress="checkFloat(event);" onkeyup="com3(this);calCustomTaxRate(true, this.value);" onfocus="this.select();" 
                                            {{ @$currency_unit == 'KRW' ? 'readonly disabled' : '' }}>
                                    </div>
                                    <div class="d-flex align-items-center col col-6 p-0 {{ (@$super_admin == 'true' || (@$state > 0 && @$state < 40)) ? 'col-sm-3 pr-2' : '' }}">
                                        <input type="text" class="form-control form-control-sm text-right mr-1" id="tariff_rate" name="tariff_rate" value="{{ @$tariff_rate ?? 0 }}" readonly>
                                        <span>%</span>
                                    </div>
                                    @if (@$super_admin == 'true' || (@$state > 0 && @$state < 40))
                                    <div class="col col-12 col-sm-3 p-0 pt-2 pt-sm-0">
                                        <a href="javascript:void(0);" onclick="return setPrdTariffRates();" class="btn btn-sm btn-outline-primary shadow-sm w-100">일괄적용</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="freight_amt" class="required">운임비/운임율</label>
                                <div class="flex_box">
                                    <div class="col col-6 col-sm-8 p-0 pr-2">
                                        <input type="text" class="form-control form-control-sm text-right" 
                                            id="freight_amt" name="freight_amt" value="{{ number_format(@$freight_amt ?? 0) }}"
                                            onkeypress="checkFloat(event);" onkeyup="com3(this);calCustomTaxRate();" onfocus="this.select();" 
                                        {{ @$currency_unit == 'KRW' ? 'readonly disabled' : '' }}>
                                    </div>
                                    <div class="d-flex align-items-center col col-6 col-sm-4 p-0">
                                        <input type="text" class="form-control form-control-sm text-right mr-1" id="freight_rate" name="freight_rate" value="{{ @$freight_rate ?? 0 }}" readonly>
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="custom_amt" class="required">(신고)금액</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm text-right" id="custom_amt" name="custom_amt" value="{{ @$custom_amt ?? 0 }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="custom_tax" class="required">통관비/통관세율</label>
                                <div class="flex_box">
                                    <div class="col col-6 col-sm-8 p-0 pr-2">
                                        <input type="text" class="form-control form-control-sm text-right" id="custom_tax" name="custom_tax" value="{{ @$custom_tax ?? 0 }}" readonly>
                                    </div>
                                    <div class="d-flex align-items-center col col-6 col-sm-4 p-0">
                                        <input type="text" class="form-control form-control-sm text-right mr-1" id="custom_tax_rate" name="custom_tax_rate" value="{{ @$custom_tax_rate ?? 0 }}" readonly>
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (@$state > 0 && @$state < 40)
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="f_sqty">파일</label>
                                <div class="d-flex">
                                    <div class="custom-file w-100">
                                        <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
                                        <label class="custom-file-label" for="file"></label>
                                    </div>
                                    <div style="min-width: 120px;">
                                        <div class="btn-group ml-2">
                                            <button class="btn btn-outline-primary apply-btn" type="button" onclick="return upload();">적용</button>
                                        </div>
                                        <a href="/sample/sample_store_stock_order.xlsx" class="ml-2" style="text-decoration: underline !important;">샘플파일</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div id="help" class="row mt-4" style="display: none;">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label style="color: #0000ff;">도움말<i class="fa fa-question-circle ml-1" aria-hidden="true"></i></label>
                                <div class="d-flex flex-column flex-md-row">
                                    <div class="d-flex flex-column" style="width: 50%; min-width: 200px; max-width:300px;">
                                        <p>기본정보</p>
                                        <ul>
                                            <li style="color: #e8554e;">수량(확정)값으로 게산</li>
                                            <li>(신고)금액 = 전체상품 총수입금액 합계</li>
                                            <li>관세율 = 관세총액 &divide; (신고)금액</li>
                                            <li>운임율 = 운임비 &divide; (신고)금액</li>
                                            <li>통관비 = 관세총액 &plus; 운임비</li>
                                            <li>통관세율 = 통관비 &divide; (신고)금액</li>
                                        </ul>
                                    </div>
                                    <div class="d-flex flex-column mt-3 mt-md-0">
                                        <p>상품정보</p>
                                        <ul>        
                                            <li style="color: #e8554e;">수량(확정)값이 0이면 수량(예정)값으로 게산</li>
                                            <li>금액 = 수량 x 단가</li>
                                            <li>수입금액 = 환율 x 단가</li>
                                            <li>총수입금액 = 환율 x 수량 x 단가</li>
                                            <li>총원가 = 총수입금액 &plus; (총수입금액 x (상품당관세율 &plus; 운임율))</li>
                                            <li>개당원가 = 총원가 &divide; 수량</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                @if (@$super_admin == 'true' || (@$state > 0 && @$state < 40))
                <a href="javascript:void(0);" onclick="cmder('{{ @$cmd }}')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                    @if (@$stock_no != "" && @$state < 30)
                    <a href="javascript:void(0);" onclick="cmder('delcmd')" class="btn btn-sm btn-primary shadow-sm pl-2">입고삭제</a>
                    @endif
                    @if ($state == 30)
                    <a href="javascript:void(0);" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm pl-2">추가입고</a>
                    <a href="javascript:void(0);" onclick="cmder('cancelcmd')" class="btn btn-sm btn-primary shadow-sm pl-2">입고취소</a>
		            @elseif (@$super_admin === 'true')
		            <a href="javascript:void(0);" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm">추가입고</a>
                    @endif
                @endif
                <a href="javascript:void(0);" onclick="return gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <a href="javascript:void(0);" onclick="return displayHelp();" class="btn btn-sm btn-outline-primary shadow-sm pl-2">도움말</a>
                <div class="mt-2">
                    @if (@$state > 0 && @$state < 40)
                    <div>
                        <a href="javascript:void(0);" onclick="return getSearchGoods();" class="btn-sm btn btn-primary" onfocus="this.blur();"><i class="fa fa-plus fa-sm mr-1"></i> 상품 추가</a>
                        @if (@$state < 30)
                        <a href="javascript:void(0);" onclick="return deleteRows();" class="btn-sm btn btn-outline-primary" onfocus="this.blur();"><i class="fa fa-trash fa-sm mr-1"></i> 상품 삭제</a>
                        @endif
                    </div>
                    @elseif (@$super_admin == 'true' && @$state == 40)
                    <p style="color:red;">* 가장 최근에 입고된 상품만 입고정보 수정이 가능합니다.</p>
                    @endif
                </div>
            </div>
        </form>
        <div id="filter-area" class="card shadow-none mb-0 ty2">
            <div class="card-header d-flex justify-content-between">
                <h4>상품 정보</h4>
                @if (@$state > 0 && @$state < 40)
                <div>
                    <a href="javascript:void(0);" onclick="return getSearchGoods();" class="btn-sm btn btn-primary" onfocus="this.blur();"><i class="fa fa-plus fa-sm mr-1"></i> 상품 추가</a>
                    @if (@$state < 30)
                    <a href="javascript:void(0);" onclick="return deleteRows();" class="btn-sm btn btn-outline-primary" onfocus="this.blur();"><i class="fa fa-trash fa-sm mr-1"></i> 상품 삭제</a>
                    @endif
                </div>
                @elseif (@$super_admin == 'true' && @$state == 40)
				<div class="d-flex align-items-center">
	                <p class="fs-14 text-danger mr-2">* 가장 최근에 입고된 상품만 입고정보 수정이 가능합니다.</p>
		            <a href="javascript:void(0);" onclick="return getSearchGoods();" class="btn-sm btn btn-primary" onfocus="this.blur();"><i class="fa fa-plus fa-sm mr-1"></i> 상품 추가</a>
				</div>
                @endif
            </div>
            <div class="card-body pt-3 pt-lg-1">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<script type="text/javascript" charset="utf-8">
    const STATE = "{{ @$state }}";
    const CMD = "{{ @$cmd }}";
    const COMMAND_URL = '/store/cs/cs01/comm';
    const StyleCenter = {"text-align": "center"};
    const StyleRight = {"text-align": "right"};

    let columns = [
        {headerName: '#', pinned: 'left', type: 'NumType', width: 50, cellStyle: StyleCenter,
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => { // 번호순으로 정렬이 안되는 문제 수정
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', width: 28, pinned: "left",
            // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30, 원가확정: 40
            headerCheckboxSelection: true,
            checkboxSelection: (params) => STATE > 0 && STATE < 40 && (STATE != 30 || !params.data.stock_prd_no),
            cellRenderer: (params) => '',
            hide: !(STATE > 0 && STATE < 40),
        },
        {field: "prd_cd", headerName: "바코드", width: 120, pinned: 'left', cellStyle: StyleCenter},
        {field: "style_no" ,headerName:"스타일넘버", width: 80, cellStyle: StyleCenter},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: StyleCenter},
        {field: "goods_nm_eng", headerName:"상품명(영문)", width: 150},
        {field: "goods_nm", headerName: "상품명", type: "HeadGoodsNameType", width: 150,
            cellRenderer: (params) => {
                if (params.data.goods_no === undefined) return '';
                if (params.data.goods_no != '0') {
                    return '<a href="javascript:void(0);" onclick="return openHeadProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                } else {
                    return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 없는 상품입니다.`);">' + params.value + '</a>';
                }
            }   
        },
        {field: "color", headerName: "컬러", width: 55, cellStyle: StyleCenter},
        {field: "color_nm", headerName: "컬러명", width: 100, cellStyle: StyleCenter},
        {field: "color_cd", headerName: "컬러코드", width: 70, cellStyle: StyleCenter},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: StyleCenter},
        {field: "item" ,headerName: "품목", width: 70, cellStyle: StyleCenter},
        {field: "brand" ,headerName:"브랜드", width: 70, cellStyle: StyleCenter},
        {field: "total_qty", headerName: "총재고", type:'currencyType', width: 60},
        {field: "sg_qty", headerName: "창고재고", type:'currencyType', width: 60},
        {field: "exp_qty", headerName: "수량(예정)", width: 70,
            editable: params => checkIsEditable(params),
            cellStyle: params => ({backgroundColor: checkIsEditable(params) ? '#ffff99' : 'none', textAlign: 'right'}),
        },
        {field: "qty", headerName: "수량(확정)", width: 70,
            editable: params => checkIsEditable(params),
            cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99', textAlign: 'right'} : {textAlign: 'right', color: '#2aa876', fontWeight: 'bold'},
        },
        {field: "unit_cost", headerName: "단가", width: 90,
            editable: params => checkIsEditable(params),
            cellStyle: params => ({backgroundColor: checkIsEditable(params) ? '#ffff99' : 'none', textAlign: 'right'}),
            valueFormatter: numberFormatter,
            cellRenderer: params => params.node.rowPinned == "top" ? "" : params.valueFormatted,
        },
        {field: "prd_tariff_rate", headerName: "상품당 관세율(%)", width: 110,
            editable: params => checkIsEditable(params),
            cellStyle: params => ({backgroundColor: checkIsEditable(params) ? '#ffff99' : 'none', textAlign: 'right'}),
            cellRenderer: params => params.node.rowPinned == "top" ? "" : Number.parseFloat(params.value || 0),
        },
        {field: "unit_total_cost", headerName: "금액", width: 80, cellStyle: StyleRight, valueFormatter: numberFormatter},
        {field: "income_amt", headerName: "수입금액(원)", width: 80, cellStyle: StyleRight, valueFormatter: KRWFormatter},
        {field: "income_total_amt", headerName: "총수입금액(원)", width: 90, cellStyle: StyleRight, valueFormatter: KRWFormatter},
        {field: "cost", headerName: "개당원가(원, VAT별도)", width: 130, cellStyle: StyleRight, valueFormatter: KRWFormatter},
        {field: "total_cost", headerName: "총원가(원, VAT별도)", width: 130, cellStyle: StyleRight, valueFormatter: KRWFormatter},
        {field: "total_cost_novat", headerName: "총원가(원, VAT포함)", width: 130, cellStyle: StyleRight, valueFormatter: KRWFormatter},
        {field: "goods_sh", headerName: "정상가", width: 70, type: "currencyType"},
        {field: "price", headerName: "현재가", width: 70, type: "currencyType"},
        {field: "recent_stock_date", headerName: "최근입고일자", width: 90, cellClass: 'hd-grid-code'},
        {field: "stock_cnt", headerName: "(품번)입고순번", width: 90, type: 'currencyType'},
        {field: "comment", headerName: "메모", width: 200,
			editable: params => checkIsEditable(params),
			cellStyle: params => ({backgroundColor: checkIsEditable(params) ? '#ffff99' : 'none'}),
        },
        {width: "auto"}
    ];

    const pApp = new App('', { gridId: "#div-gd" });
    let gx;
    const pinnedRowData = [{ 
        prd_cd: '합계', count: 0, exp_qty: 0, qty: 0, unit_cost: 0, unit_total_cost: 0, 
        income_amt: 0, income_total_amt: 0, cost: 0, total_cost: 0, total_cost_novat: 0 
    }];

    $(document).ready(() => {
        pApp.ResizeGrid(100, window.screen.width >= 740 ? undefined : 400);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => params.node.rowPinned ? ({'font-weight': 'bold', 'background-color': '#eee', 'border': 'none'}) : false, // 상단고정row styling
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (params) => onCellValueChanged(params),
            isRowSelectable: (params) => {
                return STATE > 0 && STATE < 40 && (STATE != 30 || !params.data.stock_prd_no);
            },
        });

        if (CMD == "editcmd") productListDraw();

        $('#excel_file').change(function(e) {
            if (validateFile() === false) {
                return $('.custom-file-label').html("");
            }
            $('.custom-file-label').html(this.files[0].name);
        });
    });

    /**********************************
     * Grid 사용함수
     *********************************/

    /** 수정가능한 셀인지 판단 */
    function checkIsEditable(params) {
        const cols = ['unit_cost', 'prd_tariff_rate', 'comment'];
        const super_admin = '{{ @$super_admin }}';

        // 슈퍼관리자 권한설정
        if (super_admin == 'true') cols.push('qty');

        if (
            (cols.includes(params.column?.colId || '')) 
            && STATE > 0 
            && STATE < (super_admin == 'true' ? 41 : 40)
            && (STATE < 40 || params.data?.is_last == 1)
            && params.node.rowPinned != 'top'
        ) return true; 

        return params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    }

    /** 화폐단위에 따른 단가/금액 포맷 */
    function numberFormatter(params) {
        if (document.search.currency_unit.value == "KRW") {
            return KRWFormatter(params); // 원화
        } else {
            return parseFloat(params.value).toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'); // 외화
        }
    }

    /** 원화 포맷 */
    function KRWFormatter(params) {
        return Math.round(params.value)
            .toString()
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }
</script>

<script>
    /** 송장번호 생성 */
    function getInvoiceNo() {
        const ff = document.search;
	    const com_id = ff.com_id.value;
	    const invoice_no = ff.invoice_no.value;

        if (invoice_no == '' && com_id != "") {
            axios({
                url: COMMAND_URL,
                method: 'post',
                data: {
                    cmd: 'getinvoiceno',
                    com_id: com_id
                }
            }).then((response) => {
                ff.invoice_no.value = response.data.invoice_no;
            }).catch((error) => { 
                console.log(error);
            });
        } else if (invoice_no == '' && com_id == '') {
            $('.sch-sup-company').click();
        }
    }

    /** 화폐단위 변경 */
    const _ = (selector) => {
        const result = document.querySelectorAll(selector);
        if (result == undefined) return result;
        return result.length > 1 ? result : result[0];
    };
    function changeUnit(ele) {
        const isKorean = ele.value == "KRW";

        _("#exchange_rate").readOnly = isKorean;
        _("#tariff_amt").readOnly = isKorean;
        _("#freight_amt").readOnly = isKorean;

        _("#exchange_rate").disabled = isKorean;
        _("#tariff_amt").disabled = isKorean;
        _("#freight_amt").disabled = isKorean;

        if (ele.value != "KRW") {
            document.search.exchange_rate.focus();
        } else {
            document.search.exchange_rate.value = 0;
        }
        calCustomTaxRate();
        gx.gridOptions.api.redrawRows();
    }

    /** 도움말 펼치기 */
    function displayHelp() {
        const help = document.getElementById("help");
        help.style.display = help.style.display === "none" ? "" : "none";
        pApp.ResizeGrid(200, window.screen.width >= 740 ? undefined : 400, "input-area");
    }

    /**********************************
     * 상품 관리
     *********************************/

    /** 상품검색 팝업 오픈 */
    function getSearchGoods() {
        const com_id = document.search.com_id;
        if (com_id.value === '') {
            alert("공급처를 선택해주세요.");
            return document.search.com_nm.click();
        }
        const url = '/store/api/goods/show?include_not_match=Y';
        const pop_up = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /** 선택한 상품 적용 */
    var goods_search_cmd = '';
    var goodsCallback = (row) => {
		$.ajax({
			url: COMMAND_URL,
			method: 'post',
			data: { cmd: 'search-stock-log', prd_cds: [row.prd_cd] },
			success: function (res) {
				addRow(({ ...row, ...res.data[0] }));
			},
			error: function(response, status, error) {
				const { code, msg } = response?.responseJSON;
				alert(msg);
			}
		});
    }
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) {
			$.ajax({
				url: COMMAND_URL,
				method: 'post',
				data: { cmd: 'search-stock-log', prd_cds: rows.map(row => row.prd_cd) },
				success: function (res) {
					rows.forEach(row => addRow({ ...row, ...res.data.find(d => d.prd_cd === row.prd_cd) }));
				},
				error: function(response, status, error) {
					const { code, msg } = response?.responseJSON;
					alert(msg);
				}
			});
        }
    }
    var beforeSearchCallback = (api_document) => {
        api_document.search.com_nm.value = document.search.com_nm.value;
        api_document.search.com_cd.value = document.search.com_id.value;
    };
    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount();
        row = { ...row, 
            item: row.opt_kind_nm, opt_kor: row.goods_opt,
            exp_qty: 0, qty: 0, unit_cost: 0, prd_tariff_rate: 0, unit_total_cost: 0, income_amt: 0, income_total_amt: 0, cost: 0, total_cost: 0, total_cost_novat: 0, 
            isEditable: true, count: count + 1,
        };
        gx.gridOptions.api.applyTransaction({ add: [row] });
    };

    /** 상품삭제 */
    function deleteRows() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("삭제할 상품을 선택해주세요.");

        if (STATE == -10 || STATE == 30) {
            // 입고취소 or 입고완료 => 새로등록한 상품만 삭제가능
           rows = rows.filter(r => r.isEditable); 
        } 
        // else if (STATE == 10 || STATE == 20) {
        //     // 입고대기 or 입고처리중 => 기존에 저장했던 상품도 삭제가능
        // }
        gx.gridOptions.api.applyTransaction({ remove: rows });
        calCustomTaxRate();
    }

    /** 입고상세조회 시, 기존 상품 조회 */
    function productListDraw() {
        axios({
            url: COMMAND_URL,
            method: 'post',
            data: { cmd: 'product', stock_no: document.search.stock_no.value }
        }).then((res) => {
            let rows = res.hasOwnProperty('data') && res.data.hasOwnProperty('rows') ? res.data.rows : "";
            const exchange_rate = unComma(document.search.exchange_rate.value || '0');
            if (rows && Array.isArray(rows)) {
                rows = rows.map((row, idx) => {
                    if (STATE == 10 || STATE == 20) { 
                        // 입고 대기거나 입고 처리중인 경우 체크박스 표시, 상품 삭제를 가능하게 합니다.
                        row.isEditable = true;
                    } else {
                        row.isEditable = false;
                    }
                    row.count = idx + 1;
                    row.income_amt = exchange_rate * row.unit_cost;
                    row.income_total_amt = row.income_amt * row.exp_qty;
                    return row;
                });
                gx.gridOptions.api.applyTransaction({ add : rows })
                calCustomTaxRate(false);
            }
        }).catch((error) => {
            console.log(error);
        });
    }
    
    /** GRID 상단고정ROW 업데이트 */
    function updatePinnedRow() {
        const rows = gx.getRows();
        const exchange_rate = unComma(document.search.exchange_rate.value || '0');
        let row = {};
        
        if (rows.length > 0) {
            row = rows.reduce((a, c) => ({
                    exp_qty: a.exp_qty + Number.parseFloat(c.exp_qty),
                    qty: a.qty + Number.parseFloat(c.qty),
                    unit_total_cost: a.unit_total_cost + Number.parseFloat(c.unit_total_cost),
                    income_amt: a.income_amt + Number.parseFloat(c.income_amt),
                    income_total_amt: a.income_total_amt + Number.parseFloat(c.income_total_amt),
                    cost: a.cost + Number.parseFloat(c.cost),
                    total_cost: a.total_cost + Number.parseFloat(c.total_cost),
                    total_cost_novat: a.total_cost_novat + Number.parseFloat(c.total_cost_novat),
                    p_custom_amt: a.p_custom_amt + Number.parseFloat((exchange_rate * (c.exp_qty || 0) * (c.unit_cost || 0))),
                }), { exp_qty: 0, qty: 0, unit_total_cost: 0, income_amt: 0, income_total_amt: 0, cost: 0, total_cost: 0, total_cost_novat: 0, p_custom_amt: 0 }
            );
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([{ ...pinnedRow.data, ...row }]);

        $("#custom_amt").val(Comma(Math.round(row.p_custom_amt || 0)));
    }

    /**********************************
     * 환율/세율 계산
     *********************************/

     /** 기본정보(환율/관세총액/운임비) 입력 시 세율계산 */
    async function calCustomTaxRate(calculate_prd = true, tariff_amt = 0) {
        const ff = document.search;
        const unit = ff.currency_unit.value;

        let exchange_rate = 0, freight_amt = 0, custom_amt = 0;
        if (unit != "KRW") {
            exchange_rate = unComma(ff.exchange_rate.value || '0') || 0; // 환율

            let p_qty, income_total;
            const rd = gx.getRows().reduce((a, c) => {
				p_qty = !!(c.exp_qty != 0 && c.exp_qty) ? Number.parseInt(c.exp_qty) : 0;
                // if (STATE < 30) {
                //     p_qty = !!(c.qty != 0 && c.qty) ? Number.parseInt(c.qty) 
                //         : !!(c.exp_qty != 0 && c.exp_qty) ? Number.parseInt(c.exp_qty) 
                //         : 0;
                // } else {
                //     p_qty = !!(c.qty != 0 && c.qty) ? Number.parseInt(c.qty) : 0;
                // }
                income_total = (exchange_rate * (p_qty || 0) * (c.unit_cost || 0));
                a[0] += income_total;
                a[1] += income_total * (c.prd_tariff_rate / 100);
                return a;
            }, [0, 0]);

            custom_amt = rd[0]; // (신고)금액
            if (tariff_amt != 0) tariff_amt = unComma(tariff_amt || '0') || 0
            else tariff_amt = rd[1] || unComma(ff.tariff_amt.value || '0') || 0; // 관세총액
            freight_amt = unComma(ff.freight_amt.value || '0') || 0; // 운임비
            const custom_tax = tariff_amt + freight_amt; // 통관비
            
            const tariff_rate = custom_amt < 1 ? 0 : Number.parseFloat((tariff_amt / custom_amt) * 100); // 관세율
            const freight_rate = custom_amt < 1 ? 0 : Number.parseFloat((freight_amt / custom_amt) * 100); // 운임율
            const custom_tax_rate = custom_amt < 1 ? 0 : Number.parseFloat((custom_tax / custom_amt) * 100); // 통관세율(관세+운임율)

            ff.tariff_amt.value = Comma(Math.round(tariff_amt));
            ff.tariff_rate.value = tariff_rate.toFixed(2);
            ff.freight_rate.value = freight_rate.toFixed(2);
            ff.custom_tax.value = Comma(Math.round(custom_tax));
            ff.custom_tax_rate.value = custom_tax_rate.toFixed(2);
        }

        if (calculate_prd) await gx.getRows().forEach(async (row) => await calProduct(row, unit, exchange_rate, freight_amt, custom_amt));
        updatePinnedRow();
    }

    /** GRID CELL 변경 시 세율계산 */
    async function onCellValueChanged(params) {
        if (params.oldValue == params.newValue) return;
        const row = params.data;

        // 수량(예정) 입력 시 수량(확정) 값 동일하게 입력
        if (params.column?.colId === 'exp_qty') row.qty = row.exp_qty;

        await calProduct(row);
        calCustomTaxRate();
    }

    /** 상품 개당 계산정보 적용 */
    async function calProduct(row, unit = "", exchange_rate = 0, freight_amt = 0, custom_amt = 0) {
        const ff = document.search;

        let qty = !!(row.exp_qty != 0 && row.exp_qty) ? Number.parseInt(row.exp_qty) : 0;
        // if (STATE < 30) {
        //     qty = !!(row.qty != 0 && row.qty) ? Number.parseInt(row.qty) 
        //         : !!(row.exp_qty != 0 && row.exp_qty) ? Number.parseInt(row.exp_qty) 
        //         : 0; // 수량
        // } else {
        //     qty = !!(row.qty != 0 && row.qty) ? Number.parseInt(row.qty) : 0;
        // }
        const unit_cost = Number.parseFloat(row.unit_cost || 0); // 단가
        const prd_tariff_rate = Number.parseFloat(row.prd_tariff_rate || 0); // 상품당 관세율

        if (unit == "") unit = ff.currency_unit.value; // 통화
        if (exchange_rate == 0) exchange_rate = unComma(ff.exchange_rate.value || '0') || 0; // 환율
        if (freight_amt == 0) freight_amt = unComma(ff.freight_amt.value || '0') || 0; // 운임비
        if (custom_amt == 0) custom_amt = gx.getRows().reduce((a, c) => {
            let p_qty = !!(c.exp_qty != 0 && c.exp_qty) ? Number.parseInt(c.exp_qty) : 0;
            // if (STATE < 30) {
            //     p_qty = !!(c.qty != 0 && c.qty) ? Number.parseInt(c.qty) 
            //         : !!(c.exp_qty != 0 && c.exp_qty) ? Number.parseInt(c.exp_qty) 
            //         : 0;
            // } else {
            //     p_qty = !!(c.qty != 0 && c.qty) ? Number.parseInt(c.qty) : 0;
            // }
            return a + (exchange_rate * (p_qty || 0) * (c.unit_cost || 0));
        }, 0); // 신고금액
        const freight_rate = custom_amt < 1 ? 0 : Number.parseFloat(freight_amt / custom_amt); // 운임율

        const income_amt = exchange_rate * unit_cost; // 수입금액
        const income_total_amt = income_amt * qty; // 총수입금액

        let cost, total_cost;
        if (unit == "KRW") {
            cost = unit_cost;
            total_cost = cost * qty;
        } else {
            total_cost = income_total_amt + (income_total_amt * ((prd_tariff_rate / 100) + freight_rate)); // 총원가 = 총수입금액 + (총수입금액 * (상품당관세율 + 운임율))
            cost = total_cost / (qty || 1);
        }
        let total_cost_novat = total_cost * 1.1; // 총원가 vat 포함

        await gx.gridOptions.api.applyTransaction({ 
            update: [{
                ...row,
                unit_total_cost: qty * unit_cost, // 금액
                income_amt: Math.round(income_amt), // 수입금액
                income_total_amt: Math.round(income_total_amt), // 총수입금액
                cost: Math.round(cost), // 개당원가 (원, VAT 별도)
                total_cost: Math.round(total_cost), // 총원가 (원, VAT 별도)
                total_cost_novat: Math.round(total_cost_novat), // 총원가 (원, VAT 포함)
            }] 
        });
    }

    // 관세율 -> 상품당관세율 일괄적용
    async function setPrdTariffRates() {
        const tariff_rate = $("#tariff_rate").val();

        await gx.gridOptions.api.applyTransaction({ 
            update: gx.getRows().map(row => ({...row, prd_tariff_rate: tariff_rate})),
        });

        await gx.getRows().forEach(async (row) => await calProduct(row));
        updatePinnedRow();
    }

    /**********************************
     * 입고관리
     *********************************/
    async function cmder(cmd) {
        if (cmd === "delcmd") delCmd();
        else if (cmd === "cancelcmd") cancelCmd();
        else if (["addcmd", "editcmd", "addstockcmd"].includes(cmd)) {
            if (await validate()) saveCmd(cmd);
        }
    }

    /** 입고저장 전 값 체크 */
    async function validate() {
        const ff = document.search;

        if(ff.com_id.value == "") {
            alert("공급처를 선택해 주십시오.");
            $('.sch-sup-company').click();
            return false;
        }
        if(ff.invoice_no.value == "") {
            alert("입고번호를 입력해 주십시오.");
            ff.invoice_no.focus();
            return false;
        }
        if(ff.stock_date.value.trim().length != 10) {
            alert("입고일자를 입력해 주십시오.");
            ff.stock_date.focus();
            return false;
        }
        if(ff.state.value == "") {
            alert("입고상태를 선택해 주십시오..");
            ff.state.focus();
            return false;
        }
        if(ff.currency_unit.value != "KRW") {
            if(ff.exchange_rate.value == "") {
                alert("환율를 입력해 주십시오.");
                ff.exchange_rate.focus();
                return false;
            }
            if(ff.tariff_amt.value == "") {
                alert("관세총액을 입력해 주십시오.");
                ff.tariff_amt.focus();
                return false;
            }
            if(ff.freight_amt.value == "") {
                alert("운임비를 입력해 주십시오.");
                ff.freight_amt.focus();
                return false;
            }
        }

        const rows = gx.getRows();
        if (rows.length < 1) {
            alert("입고상품을 한 개 이상 등록해주세요.");
            return false;
        }
        // for (let row of rows) {
        //     if (await checkPrdData(row) == false) return false;
        // }
        return true;
    }

    /** 입고저장 전 값 체크 시 상품데이터 값 체크 */
    // async function checkPrdData(row) {
    //     const rowIdx = row.count - 1;
    //     const unit = document.search.currency_unit.value;
    //     const { exp_qty, qty, unit_cost, prd_tariff_rate, stock_prd_no } = row;

        // if (STATE == 40 || stock_prd_no != undefined) {
        //     if ((qty || 0) == 0 && (exp_qty || 0) == 0) { // check qty
        //         alert("입고수량(확정) 또는 입고수량(예정)을 입력해주세요.");
        //         gx.gridOptions.api.stopEditing(); // stop editing
        //         gx.gridOptions.api.startEditingCell({ rowIndex: rowIdx, colKey: 'qty' });
        //         return false;
        //     }
        //     if (unit_cost == "" || unit_cost == 0) { // check unit_cost
        //         alert("단가를 입력해주세요.");
        //         gx.gridOptions.api.stopEditing(); // stop editing
        //         gx.gridOptions.api.startEditingCell({ rowIndex: rowIdx, colKey: 'unit_cost' });
        //         return false;
        //     }
        // }
        // return true;
    // }

    /** 입고저장 */
    const getFormValue = (form, key) => form.hasOwnProperty(key) ? form[key].value : '';
    function saveCmd(cmd) {
        const rows = gx.getRows();
        let state = $('#state').val();

        for (let row of rows) {
            const rowIdx = row.count - 1;
            const { exp_qty, qty, unit_cost, prd_tariff_rate, stock_prd_no } = row;
            
            if(state == 30) {
                if ((qty || 0) == 0 && (exp_qty || 0) == 0) { // check qty
                    alert("입고수량(확정) 또는 입고수량(예정)을 입력해주세요.");
                    gx.gridOptions.api.stopEditing(); // stop editing
                    gx.gridOptions.api.startEditingCell({ rowIndex: rowIdx, colKey: 'qty' });
                    return false;
                }
                // if (unit_cost == "" || unit_cost == 0) { // check unit_cost
                //     alert("단가를 입력해주세요.");
                //     gx.gridOptions.api.stopEditing(); // stop editing
                //     gx.gridOptions.api.startEditingCell({ rowIndex: rowIdx, colKey: 'unit_cost' });
                //     return false;
                // }
            }
        }

        let confirm_msg = "저장하시겠습니까?";
        if (cmd == 'addstockcmd') {
            if (rows.filter(row => !row.stock_prd_no).length < 1) return alert("추가입고할 상품이 없습니다.");
            confirm_msg = "상품을 추가입고하시겠습니까?";
        }
        if (!confirm(confirm_msg)) return;
        
        const ff = document.search;
        const data = {
            cmd: cmd,
            data: rows,
            stock_no: getFormValue(ff, 'stock_no'),
            com_id: getFormValue(ff, 'com_id'),
            invoice_no: getFormValue(ff, 'invoice_no'),
            bl_no: getFormValue(ff, 'bl_no'),
            stock_date: getFormValue(ff, 'stock_date'),
            state: getFormValue(ff, 'state'),
            loc: getFormValue(ff, 'loc'),
            currency_unit: getFormValue(ff, 'currency_unit'), // 통화
            exchange_rate: getFormValue(ff, 'exchange_rate'), // 환율
            tariff_amt: getFormValue(ff, 'tariff_amt'), // 관세총엑
            freight_amt: getFormValue(ff, 'freight_amt'), // 운임비
            custom_amt: getFormValue(ff, 'custom_amt'), // 신고금액
        };

        // console.log(data);

        axios({
            url: COMMAND_URL,
            method: 'post',
            data: data,
        }).then((response) => {
            if (response.data.code == 1) {
                alert(response.data.message);
                window.opener.Search();
                window.close();
            } else {
                alert("저장 시 오류가 발생했습니다. 관리자에게 문의해주세요.");
                console.log(response.data.message);
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    /** 입고취소 */
    function cancelCmd() {
        if (STATE != '30') return alert("입고완료시에만 입고취소가 가능합니다.");
        if (!confirm("입고취소하시겠습니까?")) return;

        axios({
            url: COMMAND_URL,
            method: 'post',
            data: { cmd : 'cancelcmd', stock_no : document.search.stock_no.value }
        }).then((res) => {
            if (res.data.code == 1) {
                window.opener.Search();
                window.close();
            } else {
                alert("입고취소 시 오류가 발생했습니다. 관리자에게 문의해주세요.");
                console.log(res.data.message);
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    /** 입고삭제 */
    function delCmd() {
        if (!confirm("입고정보를 삭제하시겠습니까?\n삭제한 정보는 되돌릴 수 없습니다.")) return;

        axios({
            url: COMMAND_URL,
            method: 'post',
            data: { cmd : 'delcmd', stock_no : document.search.stock_no.value }
        }).then((res) => {
            if (res.data.code == 1) {
                window.opener.Search();
                window.close();
            } else {
                alert('삭제에 실패했습니다. 다시 시도해주세요. 코드번호 : ' + res.data.code );
                console.log(res.data);
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    /**********************************
     * 엑셀파일 업로드
     *********************************/

    /** 파일 체크 */
    function validateFile() {
        const target = $('#excel_file')[0].files;

        if (target.length > 1) {
            alert("파일은 1개만 올려주세요.");
            return false;
        }
        if (target === null || target.length === 0) {
            alert("업로드할 파일을 선택해주세요.");
            return false;
        }
        if (!/(.*?)\.(xlsx|XLSX)$/i.test(target[0].name)) {
            alert("Excel파일만 업로드해주세요.(xlsx)");
            return false;
        }
        return true;
    }

    /** 엑셀파일 업로드 */
    function upload() {
        const file_data = $('#excel_file').prop('files')[0];
		const form_data = new FormData();
        form_data.append('cmd', 'import');
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        axios({
            method: 'post',
            url: COMMAND_URL,
            data: form_data,
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }).then(async (res) => {
            if (res.data.code == 1) {
                const file = res.data.file;
                await importExcel("/" + file);
                calCustomTaxRate();
            } else {
                console.log(res.data.message);
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    /** 엑셀파일 데이터 적용 */
    const populateGrid = async (workbook) => {
        const firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
        const worksheet = workbook.Sheets[firstSheetName];

        let com_id = worksheet['C4']?.w;
        let bl_no = worksheet['C5']?.w;
        let stock_date = worksheet['C6']?.w;
        let currency_unit = worksheet['C7']?.w;
        let exchange_rate = worksheet['C8']?.w;
        let tariff_amt = worksheet['C9']?.w;
        let freight_amt = worksheet['C10']?.w;

        const columns	= {
			'B': 'prd_cd',
			'C': 'exp_qty',
			'D': 'qty',
			'E': 'unit_cost',
			'F': 'prd_tariff_rate',
        };

        alert("상품을 순차적으로 불러오고 있습니다.\n다소 시간이 소요될 수 있습니다."); // progress

        // 입고완료 이후 정보적용 처리 필요

        // 기본정보 적용
        const ff = document.search;
        ff.com_id.value = com_id;
        ff.bl_no.value = bl_no;
        ff.stock_date.value = stock_date;
        $("#currency_unit").val(currency_unit).prop("selected", true).trigger("change");

        if (currency_unit != "KRW") {
            ff.exchange_rate.value = Comma(exchange_rate);
            ff.tariff_amt.value = Comma(tariff_amt);
            ff.freight_amt.value = Comma(freight_amt);
        }

        getInvoiceNo();

        // 상품정보 적용
        let rowIndex = 13; // 엑셀 13행부터 시작 (샘플데이터 참고)
        let count = gx.gridOptions.api.getDisplayedRowCount();
        while (worksheet['B' + rowIndex]) { // iterate over the worksheet pulling out the columns we're expecting
            let row = {};
            let ws;
            Object.keys(columns).forEach((column) => {
                if(worksheet[column + rowIndex] !== undefined) {
                    ws = worksheet[column + rowIndex];
                    if (column == 'F') row[columns[column]] = ws.v * 100;
                    else row[columns[column]] = ws.t == 'n' ? ws.v : ws.w;
                }
            });
            
            row.exp_qty ??= 0; // 수량(예정)
            row.qty ??= 0; // 수량(확정)
            row.unit_cost ??= 0;  // 단가
            if (currency_unit == "KRW") row.prd_tariff_rate = 0; // 상품당 관세율
            else row.prd_tariff_rate ??= 0;
            row = { ...row,
                goods_no: "검사중...",
                unit_total_cost: 0, income_amt: 0, income_total_amt: 0, cost: 0, total_cost: 0, total_cost_novat: 0, 
                isEditable: true, count: ++count,
            };
            gx.gridOptions.api.applyTransaction({ add : [row] });

            rowIndex++;
            await getGood(row, !worksheet['B' + rowIndex]);
        }
    };

    /** 엑셀 개별상품데이터 상세정보 조회 */
    async function getGood(row, isLast) {
        axios({
            cmd: 'getgood',
            url: COMMAND_URL,
            method: 'post',
            data: { cmd: 'getgood', prd_cd: row.prd_cd }
        }).then(async (res) => {
            const code = res.data.code; // 0: 상품없음, -1: 상품중복 또는 입점상품, 1: 존재하는 상품
            let good, message, checked_row;
            
            if (res.data.code == 1) {
                good = res.data.good;                
                checked_row = {...row, ...good};
            } else {
                message = res.data.message;
                checked_row = {...row, goods_no: message};
            }

            await gx.gridOptions.api.applyTransaction({ update : [checked_row] });
            if(isLast) {
                calCustomTaxRate();
                const com_id = document.search.com_id.value;
                const { data : { body: coms } } = await axios({  url: `/head/api/company/getlist?com_id=${com_id}`, method: "get" });
                if (coms.length > 0) document.search.com_nm.value = coms[0].com_nm;
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    /** 엑셀관련함수 */
    async function importExcel(url) {
		await makeRequest('GET',
			url,
			// success
			async (data) => {
				const workbook = convertDataToWorkbook(data);
				await populateGrid(workbook);
			},
			// error
			(error) => {
                console.log(error);
			}
		);
	};
    function convertDataToWorkbook(data) {
        data = new Uint8Array(data);
		const arr = new Array();

		for (let i = 0; i !== data.length; ++i) {
			arr[i] = String.fromCharCode(data[i]);
		}

		const bstr = arr.join("");
		return XLSX.read(bstr, {type: "binary"});
    }
    function makeRequest(method, url, success, error) {
		let httpRequest = new XMLHttpRequest();
		httpRequest.open("GET", url, true);
		httpRequest.responseType = "arraybuffer";

		httpRequest.open(method, url);
		httpRequest.onload = () => {
			success(httpRequest.response);
		};
		httpRequest.onerror = () => {
			error(httpRequest.response);
		};
		httpRequest.send();
	};
    
    /**********************************
     * 숫자데이터 입력 컨트롤
     *********************************/
    
    function checkFloat(e) {
        let key = e.keyCode;
        if ((key < 48 || key > 58) && key != 46) e.returnValue = false;
    }

    function com3(ele) {
        let str = ele.value;
        if (str != null && str != '') {
            let retStr = '', m = '', dot = '';
            let dotIdx = -1;

            str = str.replace(/^0*|\,/g,'');
            if (str.charAt(0) == "-") {
                m = "-";
                str = str.substr(1, str.length);
            }
            dotIdx = str.indexOf(".");
            if (dotIdx > 0) {
                dot = str.substr(dotIdx, str.length);
                str = str.substr(0, dotIdx);
            }

            for (let i = 0; i < str.length; i++) {
                if ((i % 3 == str.length % 3) && (i != 0)) {
                    retStr += ",";
                }
                retStr += str.charAt(i);
            }
            ele.value = `${m}${retStr}${dot}`;
        }
    }

    function unComma(input) {
        let inputString = new String;
        let outputString = new String;
        let outputNumber = new Number;
        inputString = input;
        outputString = '';
        for (let counter = 0; counter < inputString.length; counter++) {
            outputString += (inputString.charAt(counter) != ',' ? inputString.charAt(counter) : '');
        }
        outputNumber = parseFloat(outputString);
        return (outputNumber);
    }

    /**********************************
     * 기타 주석처리 함수
     *********************************/

    // 기존에 공용으로 사용하던 화폐단위 type은 소수점을 전부 버리므로 반올림으로 커스텀하여 구현하였음
    // const currencyFormatter = (params) => { 
    //     const value = Math.round(params.value).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    //     return isNaN(value) ? 0 : value;
    // };

    // const strNumToPrice = (price) => {
    //     return typeof price == 'string' ? price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : "";
    // };
    
</script>
@stop
