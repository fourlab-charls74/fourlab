@extends('store_with.layouts.layout')
@section('title','출고 > 원부자재 일반출고')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">원부자재 일반출고</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 원부자재 출고</span>
		<span>/ 일반출고</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="/store/stock/stk16" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 출고 리스트</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
                <div class="row">
                    {{-- <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="store_type">매장구분</label>
                            <div class="flex_box">
                                <select name='store_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($store_types as $store_type)
                                        <option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="store_cd">매장명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">원부자재코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='text' id="prd_cd_sub" name='prd_cd_sub' class="form-control form-control-sm w-100 ac-style-no search-enter">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd_sub"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_nm">원부자재명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='prd_nm' id="prd_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">구분</label>
                            <div class="flax_box">
                                <select name='type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($types as $type)
                                    <option value='{{ $type->code_id }}'>{{ $type->code_id }} : {{ $type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="opt">품목</label>
                            <div class="flax_box">
                                <select name='opt' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($opts as $opt)
                                    <option value='{{ $opt->code_id }}'>{{ $opt->code_id }} : {{ $opt->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                        <option value="5000">5000</option>
                                        <option value="10000">10000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="p.prd_cd">바코드</option>
                                        <option value="p.price">판매가</option>
                                        <option value="p.wonga">원가</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>

		<div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>

	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap d-flex justify-content-between">
				<div class="d-flex">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
                <div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
                    <div class="d-flex">
                        <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="ext_storage_qty" id="ext_storage_qty" value="Y" checked>
                            <label class="custom-control-label font-weight-normal" for="ext_storage_qty">창고재고 0 제외</label>
                        </div>
                    </div>
                    <span class="d-none d-lg-block mr-2 tex-secondary">|</span>
                    <div class="d-flex mb-1 mb-lg-0">
                        <span class="mr-1">창고</span>
                        <select id='storage' name='storage' class="form-control form-control-sm"  style='width:160px;display:inline'>
                            <option value=''>선택</option>
                            @foreach ($storages as $storage)
                                @if ($storage->default_yn == "Y")
                                <option value='{{ $storage->storage_cd }}' selected >{{ $storage->storage_nm }} (대표) </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-lg-0">
                        <i class="fas fa-arrow-right fa-sm ml-2 mr-2" aria-hidden="true"></i>
                        <span class="mr-1">판매채널</span>
                        <div class="flex_box" style="width:100px">
                            <select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
                                <option value=''>전체</option>
                            @foreach ($store_channel as $sc)
                                <option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-lg-0">
                        <i class="fas fa-arrow-right fa-sm ml-2 mr-2" aria-hidden="true"></i>
                        <span class="mr-1">매장구분</span>
                        <div class="flex_box" style="width:100px">
                            <select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled onchange="chg_store_channel_kind();">
                                <option value=''>전체</option>
                            @foreach ($store_kind as $sk)
                                <option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-1 mb-lg-0">
                        <i class="fas fa-arrow-right fa-sm ml-2 mr-2" aria-hidden="true"></i>
                        <span class="mr-1">매장</span>
                        <select id='store' name='store' class="form-control form-control-sm"  style='width:160px;display:inline' disabled>
                            <option value=''>선택</option>
                        </select>
                    </div>
                    <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
                    <div class="d-flex mr-1 mb-1 mb-lg-0">
                        <span class="mr-1">출고예정일</span>
                        <div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $today }}" autocomplete="off" readonly />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="docs-datepicker-container"></div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <select id='rel_order' name='rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
                            @foreach ($rel_orders as $rel_order)
                                <option value='{{ $rel_order->code_id }}'>{{ $rel_order->code_val }}</option>
                            @endforeach
                        </select>
                        <a href="#" onclick="requestRelease();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>출고요청</a>
                    </div>
                </div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">

    const DEFAULT = { lineHeight : "30px" };

    let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
        {
            field: "type_nm",
            headerName: "구분",
            width: 70,
            cellStyle: DEFAULT
        },
        {
            field: "opt",
            headerName: "품목",
            width: 80,
            cellStyle: DEFAULT
        },
        {field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: DEFAULT, surl:"{{config('shop.front_url')}}"},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "prd_nm", headerName: "원부자재명", width: 200},
        {field: "prd_cd", headerName: "바코드", width:120, cellStyle: DEFAULT,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return ShowProduct(\'' + params.value + '\');">' + params.value + '</a>';
                }
            }
        },
        {
            field: "color",
            headerName: "칼라",
            cellStyle: DEFAULT,
            width: 100
        },
        {
            field: "size",
            headerName: "사이즈",
            cellStyle: DEFAULT,
            width: 90
        },
        {
            field: "unit",
            headerName: "단위",
            cellStyle: DEFAULT,
            width: 120
        },
        {
            field: "price",
            headerName: "판매가",
            type: 'currencyType',
            cellStyle: DEFAULT,
            width: 80
        },
        {
            field: "wonga",
            headerName: "원가",
            type: 'currencyType',
            cellStyle: DEFAULT,
            width: 80
        },
		{headerName: "창고보유재고",
            children: [
                @foreach (@$storages as $storage)
                    @if ($storage->default_yn == "Y")
                    {field: '{{ $storage->storage_cd }}', headerName: '{{ $storage->storage_nm }}', type: "numberType",
                        cellRenderer: function(params) {
                            let storage_cd = '{{ $storage->storage_cd }}';
                            let arr = params.data.storage_qty.filter(s => s.storage_cd === storage_cd);
                            if (arr.length > 0) {
                                return arr[0].wqty;
                            }
                            return 0;
                        }
                    },
                    @endif
                @endforeach
            ],
        },
		{field: "rel_qty", headerName: "요청수량", type: "numberType", editable: true, cellStyle: {'background-color': '#ffff99'}},
        {field: "amount", headerName: "합계", type: 'currencyType', width:100, valueGetter: (params) => calAmount(params)},
        {width: 'auto'}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
                if (e.column.colId == "rel_qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    }
                }
            }
        });
        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
        data += "&ext_storage_qty=" + $("[name=ext_storage_qty]").is(":checked");
		gx.Request('/store/stock/stk18/search', data, 1);
	}

    function ShowProduct(product_code) {
        var url = '/store/product/prd03/show/' + product_code;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=555");
    }

    const calAmount = (params) => {
        const row = params.data;
        const result = parseInt(row.price) * parseInt(row.rel_qty);
        return isNaN(result) ? 0 : result;
    };

    // 출고요청
    function requestRelease() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");
        if (rows.filter(r => !r.rel_qty || !r.rel_qty.trim() || r.rel_qty == 0 || isNaN(parseInt(r.rel_qty))).length > 0) return alert("선택한 상품의 요청수량을 입력해주세요.");

        let storage_cd = $('[name=storage]').val();
        if (storage_cd === '') return alert("상품을 출고할 창고를 선택해주세요.");
        
        let store_channel = $('[name=store_channel]').val();
        if (store_channel === '') return alert("상품을 출고할 판매채널을 선택해주세요.");
        
        let store_channel_kind = $('[name=store_channel_kin]').val();
        if (store_channel_kind === '') return alert("상품을 출고할 매장구분을 선택해주세요.");

        let store_cd =$('[name=store]').val();
        if (store_cd === '') return alert("상품을 보낼 매장을 선택해주세요.");

        let over_qty_rows = rows.filter(row => {
            let cur_storage = row.storage_qty.filter(s => s.storage_cd === storage_cd);
            if (cur_storage.length > 0) {
                if (cur_storage[0].wqty < parseInt(row.rel_qty)) {
                    return true;
                } else {
                    return false;
                }
            }
            return true; // 상품재고가 없는경우
        });
        if (over_qty_rows.length > 0) return alert(`선택하신 창고의 재고보다 많은 수량을 요청하실 수 없습니다.\n바코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);

        if (!confirm("해당 상품을 출고요청하시겠습니까?")) return;

        const data = {
            products: rows, 
            storage_cd, 
            store_cd,
            exp_dlv_day: $('[name=exp_dlv_day]').val(),
            rel_order: $('[name=rel_order]').val(),
        };

        axios({
            url: '/store/stock/stk18/request-release',
            method: 'post',
            data: data,
        }).then(function (res) {
            if (res.data.code === 200) {
                if (!confirm("일반출고 요청 및 접수가 정상적으로 등록되었습니다." + "\n출고요청을 계속하시겠습니까?")) {
                    location.href = "/store/stock/stk16";
                } else {
                    Search();
                }
            } else {
                // console.log(res.data);
                alert("출고요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            // console.log(err);
        });
    }

  

    /**
     * 일반출고 매장구분 추가 후 매장 구분 선택시 해당되는 매장 출력
     */
    function change_store_type(e) {

        let store_type = document.getElementById('store_type').value;

        $.ajax({
            method: 'get',
            url: '/store/stock/stk18/chg-store-type',
            data: {
                store_type : store_type
            },
            success: function (res) {
                if(res.code === 200) {
                    $('#store').empty();
                    $('#store').append("<option value=''>선택</option>");
                    for(let i = 0; i <= res.result.length ; i++) {
                        if(store_type == '06') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '07') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '08') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '10') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '11') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '12') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '13') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '15') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        } else if (store_type == '') {
                            $('#store').append("<option value='" + res.result[i].store_cd +"'>" + res.result[i].store_nm + "</option>");
                        }
                    }
                }else {
                }
            },
            error: function(error) {
                console.log(error)
            }
        });        
    }

    // 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
	$(document).ready(function() {
		const store_channel = document.getElementById("store_channel");
		const store_channel_kind = document.getElementById("store_channel_kind");
		const store = document.getElementById("store");

		store_channel.addEventListener("change", () => {
			if (store_channel.value) {
				store_channel_kind.disabled = false;
                $('#store').empty();
                let select =  $("<option value=''>전체</option>");
                $('#store').append(select);
			} else {
				store_channel_kind.disabled = true;
			}
		});

		store_channel_kind.addEventListener("change", () => {
			if (store_channel.value) {
				store.disabled = false;
			} else {
				store.disabled = true;
			}
		});
	});

	// 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
	function chg_store_channel() {

		const sel_channel = document.getElementById("store_channel").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel',
			data: {
				'store_channel' : sel_channel
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#store_channel_kind').append(option);
					}

				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}
    // 매장구분이 변경되면 해당 매장구분의 매장을 가져오는 부분
	function chg_store_channel_kind() {

		const sel_channel = document.getElementById("store_channel").value;
		const sel_channel_kind = document.getElementById("store_channel_kind").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel_kind',
			data: {
				'store_channel_kind' : sel_channel_kind
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store').append(select);

					for(let i = 0; i < res.stores.length; i++) {
						let option = $("<option value="+ res.stores[i].store_cd +">" + res.stores[i].store_nm + "</option>");
						$('#store').append(option);
					}

				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}	
</script>
@stop
