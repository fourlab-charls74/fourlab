@extends('store_with.layouts.layout-nav')
@php
    $title = "판매유형등록";
    if($cmd == "update") $title = "판매유형관리 - " . @$sale_type->sale_type_nm;
@endphp
@section('title', $title)

@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 코드관리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Cmder('{{ $cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
            {{-- @if($cmd == "update")
            <a href="javascript:void(0)" onclick="Cmder('delete')" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i>삭제</a>
            @endif --}}
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
        </div>
    </div>

    <style> 
        .required:after {content:" *"; color: red;}
        .table th {min-width:120px;}

        @media (max-width: 740px) {
            .table td {float: unset !important;width:100% !important;}
        }
    </style>

	<form name="f1" id="f1">
		<input type="hidden" name="cmd" id="cmd" value="{{ $cmd }}">
		<div class="card_wrap aco_card_wrap mb-3">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">기본 정보</a>
				</div>
				<div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">판매구분</th>
                                            <td colspan="3">
                                                <div class="d-flex">
                                                    @if($cmd == "add")
                                                    <select id="sale_kind" name="sale_kind" class="form-control form-control-sm w-100">
                                                        <option value="">전체</option>
                                                        @foreach ($sale_kinds as $sale_kind)
                                                        <option value="{{ $sale_kind->code_id }}">
                                                            [{{ $sale_kind->code_id }}] {{ $sale_kind->code_val }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @else
                                                    <input type="hidden" id="sale_kind" name="sale_kind" value="{{ @$sale_type->sale_kind }}" />
                                                    <p>[{{ @$sale_type->sale_kind }}] {{ @$sale_type->sale_type_nm }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            {{-- <th class="required">판매유형명</th>
                                            <td>
                                                <div class="form-inline">
                                                    <input type="text" name="sale_type_nm" id="sale_type_nm" value="{{ @$sale_type->sale_type_nm }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td> --}}
                                            <th>기준금액</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="sale_apply_P" name="sale_apply" value="price" @if(@$sale_type->sale_apply != 'tag') checked @endif />
                                                        <label class="custom-control-label" for="sale_apply_P">판매가</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="sale_apply_T" name="sale_apply" value="tag" @if(@$sale_type->sale_apply == 'tag') checked @endif />
                                                        <label class="custom-control-label" for="sale_apply_T">Tag가</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>사용여부</th>
                                            <td colspan="3">
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="use_yn_Y" name="use_yn" value="Y" @if(@$sale_type->use_yn != 'N') checked @endif />
                                                        <label class="custom-control-label" for="use_yn_Y">Y</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="use_yn_N" name="use_yn" value="N" @if(@$sale_type->use_yn == 'N') checked @endif />
                                                        <label class="custom-control-label" for="use_yn_N">N</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>적용구분</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="amt_kind_P" name="amt_kind" value="per" @if(@$sale_type->amt_kind != 'amt') checked @endif />
                                                        <label class="custom-control-label" for="amt_kind_P">할인율</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="amt_kind_A" name="amt_kind" value="amt" @if(@$sale_type->amt_kind == 'amt') checked @endif />
                                                        <label class="custom-control-label" for="amt_kind_A">할인액</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>할인율/액</th>
                                            <td>
                                                <div class="form-inline">
                                                    <div class="d-flex align-items-center w-100">
                                                        <input type="text" name="sale_val" id="sale_val" value="@if(@$sale_type->amt_kind == 'amt'){{ @$sale_type->sale_amt ?? 0 }}@else{{ @$sale_type->sale_per ?? 0 }}@endif" class="form-control form-control-sm text-right w-100" />
                                                        <span class="ml-2 fs-14" id="amt_kind_unit">@if(@$sale_type->amt_kind == 'amt') 원 @else % @endif</span>
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
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header d-flex justify-content-between align-items-center mb-0">
					<a href="#">매장 정보</a>
                    <div class="d-flex align-items-center">
                        매장구분 : 
                        <select id="store_type" name="store_type" class="form-control ml-2 mr-1" style="width:120px;height:33px;">
                            <option value="">전체</option>
                            @foreach ($store_types as $store_type)
                            <option value="{{ $store_type->code_id }}">
                                {{ $store_type->code_val }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-sm btn-primary shadow-sm p-1 pl-3 pr-3" onclick="Search()">조회</button>
                    </div>
				</div>
				<div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</form>
</div>

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "use_yn", headerName: "사용", cellStyle: {"text-align": "center"}, 
            cellRenderer: function(params) {
                return `<input type="checkbox" onclick="changeUseYnVal(event, '${params.rowIndex}')" style="width:15px;height:15px;" ${params.value === 'Y' ? "checked" : ""} />`;
        }},
        {field: "store_cd", headerName: "매장코드", width: 100, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "매장명", width: 200},
        {field: "sdate", headerName: "시작일", width: 100, cellStyle: {"text-align": "center", "background-color": "#ffff99"}, editable: true},
        {field: "edate", headerName: "종료일", width: 100, cellStyle: {"text-align": "center", "background-color": "#ffff99"}, editable: true},
        {headerName: "", width: "auto"},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(480);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: (e) => {
                e.node.data.use_yn = 'Y';
                gx.gridOptions.api.updateRowData({update: [e.node.data]});
                e.node.setSelected(true);
            }
        });
        Search();

        setAmtKindText();
    });

    // 매장정보 검색
    function Search() {
        let sale_type_cd = "{{ @$sale_type->idx }}";
        let store_type_cd = $("[name=store_type]").val();
        gx.Request("/store/standard/std05/search-store/" + sale_type_cd, "store_type_cd=" + store_type_cd, -1, function(d) {
            gx.gridOptions.api.forEachNodeAfterFilter(node => {
                if(node.data.use_yn === 'Y') node.setSelected(true);
            });
        });
    }

    // 매장별 판매유형 사용여부 변경
    function changeUseYnVal(e, rowIndex) {
        const node = gx.getRowNode(rowIndex);
        node.data.use_yn = e.target.checked ? 'Y' : 'N';
        node.setSelected(e.target.checked);
    }

    // 판매유형 등록 / 수정
    function Cmder(type) {
        if(!validation(type)) return;
        if(!window.confirm("판매유형 정보를 저장하시겠습니까?")) return;

        let url = '/store/standard/std05/' + type;
        let method = type === 'add' ? 'post' : 'put';

        axios({
            url: url,
            method: method,
            data: getFormData(type),
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                if(type === 'add') {
                    location.href = "/store/standard/std05/show/" + res.data.data.sale_type_cd;;
                }
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 저장 시 입력값 확인
    const validation = (cmd) => {
        if(cmd === "add") {
            // 판매구분 선택여부
            if(f1.sale_kind.value === '') {
                f1.sale_kind.focus();
                return alert("판매구분을 선택해주세요.");
            }
        }

        if(isNaN(f1.sale_val.value)) {
            f1.sale_val.focus();
            return alert("할인율/액의 값을 숫자형태로 입력해주세요.");
        }
        
        if(f1.amt_kind.value === 'per' && f1.sale_val.value > 100) {
            f1.sale_val.focus();
            return alert("할인율은 100%를 초과할 수 없습니다.");
        }

        return true;
    }

    // 저장데이터 반환
    const getFormData = (cmd) => {
        let sale_types = <?= json_encode(@$sale_kinds) ?> ;
        let sale_type = sale_types.find(s => s.code_id === f1.sale_kind.value);
        
        return {
            sale_kind_cd: cmd === 'update' ? "{{ @$sale_type->idx }}" : '',
            sale_kind: f1.sale_kind.value,
            sale_type_nm: sale_type ? sale_type['code_val'] : '',
            sale_apply: f1.sale_apply.value,
            amt_kind: f1.amt_kind.value,
            sale_amt: f1.amt_kind.value === 'amt' ? f1.sale_val.value : '',
            sale_per: f1.amt_kind.value === 'per' ? f1.sale_val.value : '',
            use_yn: f1.use_yn.value,
            store_datas: gx.getRows(),
        }
    }

    // 적용구분 값 변경 시 할인율/액 단위 text 변경
    function setAmtKindText() {
        $("[name=amt_kind]").on("change", function(e) {
            let unit = e.target.value === 'per' ? '%' : '원';
            $("#amt_kind_unit").text(unit);
        });
    }
</script>
@stop
