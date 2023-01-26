@extends('store_with.layouts.layout-nav')
@section('title', '택배송장 일괄등록')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">택배송장 일괄등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 주문/배송관리</span>
                <span>/ 온라인 배송처리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="return completeOrder()" class="btn btn-primary mr-1">출고완료처리</a>
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 100px;}
        .table td {width: 45%;}
        
        @media (max-width: 740px) {
            .table td {float: unset !important;width: 100% !important;}
        }
    </style>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">기본정보 & 파일 업로드</a>
            </div>
            <div class="card-body">
                <form name="f1">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                          <th>택배사</th>
                                          <td>
                                            <div class="flex_box form-inline">
                                              <select id='u_dlvs' name='u_dlvs' class="form-control form-control-sm w-100 mr-2">
                                                <option value="">전체</option>
                                                  @foreach (@$dlvs as $dlv)
                                                      <option value='{{ $dlv->code_id }}'{{ $dlv->code_id === $dlv_cd ? 'selected' : '' }}>{{ $dlv->code_val }}</option>
                                                  @endforeach
                                              </select>
                                            </div>
                                          </td>
                                          <th>SMS 발송</th>
                                          <td>
                                            <div class="custom-control custom-checkbox form-check-box mr-2">
                                                <input type="checkbox" name="send_sms_yn" id="send_sms_yn" class="custom-control-input" checked="" value="Y">
                                                <label class="custom-control-label text-left" for="send_sms_yn" style="line-height:27px;justify-content:left">배송 문자 발송</label>
                                            </div>
                                          </td>
                                        </tr>
                                        <tr>
                                            <th>파일</th>
                                            <td colspan="3" class="pb-1">
                                                <div>
                                                    <div class="flex_box">
                                                        <div class="custom-file w-50">
                                                            <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
                                                            <label class="custom-file-label" for="file"></label>
                                                        </div>
                                                        <div class="btn-group ml-2">
                                                            <button class="btn btn-outline-primary apply-btn" type="button" onclick="upload();">적용</button>
                                                        </div>
                                                        <a href="/sample/sample_dlvno_batch.xlsx" class="ml-2" style="text-decoration: underline !important;">택배송장 일괄등록 양식 다운로드</a>
                                                    </div>
                                                    <p class="mt-2 text-danger">* 온라인 주문접수된 주문건만 적용됩니다.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">주문정보</a>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
        {field: "result", headerName: "처리결과", pinned: 'left', width: 100, cellStyle: (params) => ({...StyleLineHeight, "color": "white", "background-color": !params.value ? '#999999' : params.value == '200' ? '#00ff00' : '#ff0000'}),
            cellRenderer: (params) => !params.value ? '미처리' : params.value  == '200' ? '성공' : ('실패(' + (out_order_errors[params.value] || '') + ')')
        },
        {field: "rel_order", headerName: "출고차수", pinned: 'left', width: 100, cellStyle: {'text-align': 'center'}},
        {field: "dlv_no", headerName: "송장번호", pinned: 'left', width: 120, editable: (params) => params.data.state < 30, cellStyle: (params) => ({'text-align': 'center', 'background-color': params.data.state < 30 ? '#ffff99' : 'none'})},
        {field: "dlv_location_cd", headerName: "배송처코드", pinned: 'left', width: 70, 
            cellStyle: (params) => ({'text-align': 'center', 'color': params.data.dlv_location_type === 'STORAGE' ? '#0000ff' : '#ff0000', 'background-color': params.data.dlv_location_type === 'STORAGE' ? '#D9E3FF' : '#FFE9E9'}),
        },
        {field: "dlv_location_nm", headerName: "배송처명", pinned: 'left', width: 100, 
            cellStyle: (params) => ({'text-align': 'center', 'color': params.data.dlv_location_type === 'STORAGE' ? '#0000ff' : '#ff0000', 'background-color': params.data.dlv_location_type === 'STORAGE' ? '#D9E3FF' : '#FFE9E9'}),
        },
        {field: "ord_no", headerName: "주문번호", pinned: 'left', width: 135,
            cellRenderer: (params) => {
                return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + params.data?.ord_no + '\',\'' + params.data?.ord_opt_no +'\');">'+ params.value +'</a>';
            }
        },
        {field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 60, cellStyle: {'text-align': 'center'},
            cellRenderer: (params) => {
                return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + params.data?.ord_no + '\',\'' + params.data?.ord_opt_no +'\');">'+ params.value +'</a>';
            }
        },
        {field: "ord_state_nm", headerName: "주문상태", width: 70, cellStyle: StyleOrdState},
        {field: "pay_stat_nm", headerName: "입금상태", width: 55, cellStyle: {'text-align': 'center'}},
        {field: "ord_type_nm", headerName: "주문구분", width: 60, cellStyle: {'text-align': 'center'}},
        {field: "ord_kind_nm", headerName: "출고구분", width: 60, cellStyle: StyleOrdKind},
        {field: "sale_place_nm", headerName: "판매처", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "goods_no", headerName: "상품번호", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "prd_cd_p", headerName: "코드일련", width: 90, cellStyle: {"text-align": "center"}},
        {field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "goods_nm", headerName: "상품명", width: 150,
            cellRenderer: function (params) {
                return '<a href="#" onclick="return openHeadProduct(\'' + params.data?.goods_no + '\');">' + params.value + '</a>';
			}
        },
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 130},
        {field: "qty", headerName: "수량", width: 50, type: "currencyType", cellStyle: {"font-weight": "bold", 'background-color': '#D5FFDA'}, aggFunc: "first"},
        @foreach (@$dlv_locations as $loc)
            {field: "{{ $loc->seq }}_{{ $loc->location_type }}_{{ $loc->location_cd }}_qty", headerName: "{{ $loc->location_nm }}", width: 100, type: "currencyType",
                cellStyle: (params) => (
                    {
                        'color': params.data.dlv_location_cd === '{{ $loc->location_cd }}' ? (params.data.dlv_location_type === 'STORAGE' ? '#0000ff' : '#ff0000') : 'none', 
                        'background-color': params.data.dlv_location_cd === '{{ $loc->location_cd }}' ? (params.data.dlv_location_type === 'STORAGE' ? '#D9E3FF' : '#FFE9E9') : 'none'
                    }
                ),
                onCellDoubleClicked: (e) => {
                    if (e.data && e.value >= e.data.qty) e.node.setDataValue('dlv_place', "{{ $loc->location_nm }}");
                }
            },
        @endforeach
        {field: "user_nm", headerName: "주문자(아이디)", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "r_nm", headerName: "수령자", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "wonga", headerName: "원가", width: 60, type: "currencyType"},
        {field: "goods_sh", headerName: "TAG가", width: 60, type: "currencyType"},
        {field: "goods_price", headerName: "자사몰판매가", width: 85, type: "currencyType"},
        {field: "price", headerName: "판매가", width: 60, type: "currencyType"},
        {field: "dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType"},
        {field: "sale_kind_nm", headerName: "판매유형", width: 100, cellStyle: {"text-align": "center"}},
        {field: "pr_code_nm", headerName: "행사구분", width: 60, cellStyle: {"text-align": "center"}},
        {field: "dlv_amt", headerName: "배송비", width: 60, type: "currencyType"},
        {field: "sales_com_fee", headerName: "판매수수료", width: 80, type: "currencyType"},
        {field: "pay_type_nm", headerName: "결제방법", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "baesong_kind", headerName: "배송구분", width: 60, cellStyle: {'text-align': 'center'}},
        {field: "ord_date", headerName: "주문일시", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "pay_date", headerName: "입금일시", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "req_nm", headerName: "접수자", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "receipt_date", headerName: "접수일시", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "receipt_comment", headerName: "접수메모", width: 150},
    ];
</script>

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(380);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
            },
            isRowSelectable: (params) => {
                return params.data.state < 30;
            },
        });

        $('#excel_file').on('change', function(e){
            if (validateFile() === false) {
                $('.custom-file-label').html("");
                return;
            }
            $('.custom-file-label').html(this.files[0].name);
        });
	});

    /**
     * 아래부터 엑셀 관련 함수들
     * - read the raw data and convert it to a XLSX workbook
     */

    // 선택파일형식 검사
    const validateFile = () => {
        const target = $('#excel_file')[0].files;

        if (target.length > 1) return alert("파일은 1개만 올려주세요.");

        if (target === null || target.length === 0) return alert("업로드할 파일을 선택해주세요.");

        if (!/(.*?)\.(xlsx|XLSX)$/i.test(target[0].name)) return alert("Excel파일만 업로드해주세요.(xlsx)");

        return true;
    };
    
    const convertDataToWorkbook = (data) => {
		/* convert data to binary string */
		data = new Uint8Array(data);
		const arr = new Array();

		for (let i = 0; i !== data.length; ++i) {
			arr[i] = String.fromCharCode(data[i]);
		}

		const bstr = arr.join("");

		return XLSX.read(bstr, {type: "binary"});
	};

	const makeRequest = (method, url, success, error) => {
		var httpRequest = new XMLHttpRequest();
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

	const populateGrid = async (workbook) => {
		var firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
		var worksheet = workbook.Sheets[firstSheetName];

		var excel_columns = {
			'A': 'ord_opt_no',
			'B': 'dlv_no',
		};

        var firstRowIndex = 4; // 엑셀 4행부터 시작 (샘플데이터 참고)
		var rowIndex = firstRowIndex; 

        let count = gx.gridOptions.api.getDisplayedRowCount();
        let rows = [];
		while (worksheet['A' + rowIndex].w) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if(item !== undefined && item.w) {
					row[excel_columns[column]] = item.w;
				}
			});
            
            rows.push(row);
            rowIndex++;
		}
        if(rows.length < 1) return alert("한 개 이상의 주문정보를 입력해주세요.");
        await getOrders(rows, firstRowIndex);
	};
    
	const importExcel = async (url) => {
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

    const upload = () => {
        if(gx.getRows().length > 0 && !confirm("새로 적용하시는 경우 기존정보는 저장되지 않습니다.\n적용하시겠습니까?")) return;
        
		const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

		const form_data = new FormData();
        form_data.append('cmd', 'import');
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        axios({
            method: 'post',
            url: '/store/order/ord03/batch-import',
            data: form_data,
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }).then(async (res) => {
            gx.gridOptions.api.setRowData([]);
            if (res.data.code == 1) {
                const file = res.data.file;
                await importExcel("/" + file);
            } else {
                console.log(res.data.message);
            }
        }).catch((error) => {
            console.log(error);
        });
        
		return false;
	};

    const getOrders = async (rows, firstIndex) => {
        axios({
            url: '/store/order/ord03/search-orders',
            method: 'post',
            data: { data: rows },
        }).then(async (res) => {
            await gx.gridOptions.api.applyTransaction({add : res.data.body});
        }).catch((error) => {
            console.log(error);
        });
    };

    // 상품반품 일괄등록
    function completeOrder() {
        let rows = gx.getRows();
        console.log(rows);

        // validation
        if(!$("#u_dlvs").val()) return alert("택배사를 선택해주세요.");
        if(rows.filter(r => r.ord_state != 20).length > 0) return alert("출고처리중 상태의 주문건만 처리가 가능합니다.");
        if(rows.filter(r => r.ord_kind > 20).length > 0) return alert("출고보류중인 주문건은 처리할 수 없습니다.");
        if(rows.filter(r => !r.dlv_no).length > 0) return alert("송장번호가 입력되지 않은 주문건이 있습니다.\n확인 후 다시 처리해주세요.");

        if(!confirm("일괄등록하신 주문건을 출고완료처리하시겠습니까?")) return;
        return;

        axios({
            url: '/store/order/ord03/complete',
            method: 'post',
            data: { 
                send_sms_yn: $("#send_sms_yn:checked").val(),
                u_dlvs: $("#u_dlvs").val(),
                data: rows
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                if (res.data.failed_rows.length > 0) alert("온라인주문이 출고완료되었으나 재고부족 등의 사유로 배송처리에 실패한 주문건이 존재합니다.\n주문번호 확인 후 다시 시도해주세요.\n해당주문건 : " + res.data.failed_rows.join(", "));
                else alert("출고완료처리가 정상적으로 완료되었습니다.");

                Search();
            } else {
                console.log(res.data);
                alert("출고완료처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop
