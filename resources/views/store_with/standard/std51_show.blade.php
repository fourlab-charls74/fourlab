@extends('store_with.layouts.layout-nav')
@section('title','코드 상세')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">코드</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 코드 - {{ $code }}</span>
                </div>
            </div>
        </div>
        <!-- FAQ 세부 정보 -->
        <form name="detail">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">코드상세</a>
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
                                                <th>구분</th>
                                                <td>
                                                    <div class="input_box">
                                                        <input type='text' class="form-control form-control-sm search-all" name='code_kind_cd' id='code_kind_cd' value='{{@$data_code_kind->code_kind_cd}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>코드명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='code_kind_nm' id="code_kind_nm" value='{{@$data_code_kind->code_kind_nm}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>영문명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='code_kind_nm_eng' id="code_kind_nm_eng" value='{{@$data_code_kind->code_kind_nm_eng}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if(@$data_code_kind->use_yn != 'N') checked @endif />
                                                            <label class="custom-control-label" for="use_y">사용</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if(@$data_code_kind->use_yn == 'N') checked @endif />
                                                            <label class="custom-control-label" for="use_n">미사용</label>
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
                @if ($code !== '')
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">코드정보</a>
                    </div>
                    <div class="card-body pt-2">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box px-0 mx-0">
                                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DataAdd();"><span class="fs-12">코드추가</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DataSave();"><span class="fs-12">코드저장</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DataMod();"><span class="fs-12">코드수정</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DataDel();"><span class="fs-12">코드삭제</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return ChangeSeq();"><span class="fs-12">순서변경</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return ChangeYN();"><span class="fs-12">사용여부변경</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </form>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
            @if ($code !== '')
                <a href="javascript:Delete();;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
            @endif
            <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
        </div>
    </div>
    <script>

        let code = '{{ $code  }}';

        /**
         * @return {boolean}
         */
        function Save() {

            if ($('#code_kind_cd').val() === '') {
                $('#code_kind_cd').focus();
                alert('구분을 선택해주세요.');
                return false;
            }

            if ($('#code_kind_nm').val() === '') {
                $('#code_kind_nm').focus();
                alert('코드명을 입력해 주세요.');
                return false;
            }

            if ($('#code_kind_nm_eng').val() === '') {
                $('#code_kind_nm_eng').focus();
                alert('영문명을 입력해 주세요.');
                return false;
            }

            if(!confirm('저장하시겠습니까?')){
                return false;
            }

            var frm = $('form');
            //console.log(frm.serialize());

            if(code == ""){
                $.ajax({
                    method: 'post',
                    url: '/store/standard/std51',
                    data: frm.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            alert("정상적으로 저장 되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            } else {
                $.ajax({
                    method: 'put',
                    url: '/store/standard/std51/' + code,
                    data: frm.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        // console.log(res);
                        if(res.code == '200'){
                            alert("정상적으로 변경 되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }


            return true;
        }

        function Delete() {
            if(confirm('삭제 하시겠습니까?')){
                $.ajax({
                    method: 'delete',
                    url: '/store/standard/std51/' + code,
                    dataType: 'json',
                    success: function (res) {
                        // console.log(response);
                        if(res.code == '200'){
                            alert("삭제되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
        }
    </script>
    <script>
        const columns = [
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
            {field: "code_id", headerName: "아이디",rowDrag: true,
                editable: function(params){ return (params.data !== undefined && params.data.editable === 'Y')? true:false; },
                cellClass:function(params){ return (params.data !== undefined && params.data.editable == 'Y')? ['hd-grid-edit']: [];},
                width:100
            },
            {field: "code_val", headerName: "코드값1",
                // editable: function(params){ return (params.data !== undefined && params.data.editable === 'Y')? true:false; },
                editable: true,
                cellStyle: {'background' : '#ffff99'},
                cellClass:function(params){ return (params.data !== undefined && params.data.editable == 'Y')? ['hd-grid-edit']: [];},
                width:120
            },
            {field: "code_val2", headerName: "코드값2",
                editable: function(params){ return (params.data !== undefined && params.data.editable === 'Y')? true:false; },
                editable: true,
                cellStyle: {'background' : '#ffff99'},
                cellClass:function(params){ return (params.data !== undefined && params.data.editable == 'Y')? ['hd-grid-edit']: [];},
                width:120
            },
            {field: "code_val3", headerName: "코드값3",
                editable: function(params){ return (params.data !== undefined && params.data.editable === 'Y')? true:false; },
                editable: true,
                cellStyle: {'background' : '#ffff99'},
                cellClass:function(params){ return (params.data !== undefined && params.data.editable == 'Y')? ['hd-grid-edit']: [];},
                width:120
            },
            {field: "code_val_eng", headerName: "영문코드값",
                editable: function(params){ return (params.data !== undefined && params.data.editable === 'Y')? true:false; },
                cellClass:function(params){ return (params.data !== undefined && params.data.editable == 'Y')? ['hd-grid-edit']: [];},
                width:120
            },
            {field: "use_yn", headerName: "사용여부", width: 58,cellClass: 'hd-grid-code'},
            {field: "admin_nm", headerName: "작성자",width: 120},
            {field: "rt", headerName: "작성일시", width: 130,cellClass: 'hd-grid-code'},
            {field: "ut", headerName: "수정일시", width: 130,cellClass: 'hd-grid-code'},
            {field: "editable",hide:true},
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(550);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            gx.gridOptions.rowDragManaged = true;
            gx.gridOptions.animateRows = true;
            Search();
        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/store/standard/std51/' + code + '/search', data);
        }

        /**
         * @return {boolean}
         */
        function DataAdd(){

            var newData = {
                chk: '',
                code_id: '',
                use_yn:'Y',
                editable:'Y'
            };

            gx.gridOptions.api.applyTransaction({
                add: [newData],
                addIndex: 0,
            });
            //gx.gridOptions.defaultColDef.editable = true;
        }

		/**
		 * @return {boolean}
		 */
		function DataSave(){

			data_codes = [];
			let checkRows = gx.getSelectedRows();

			checkRows.map(function(row) {
				if(row.editable === 'Y'){
					data_codes.push(row);
				}
			});

			if(data_codes.length == 0 ){
				alert('저장할 코드정보를 선택해 주십시오.');
				return false;
			}

			$.ajax({
				method: 'post',
				url: '/store/standard/std51/' + code + '/save',
				data: {data:JSON.stringify(data_codes)},
				dataType: 'json',
				success: function (res) {
					if(res.code == '200'){
						Search();
					} else {
						alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
						console.log(res.msg);
					}
				},
				error: function(e) {
					console.log(e.responseText)
				}
			});
		}

        function DataMod(){

            let checkRows = gx.getSelectedRows();

            console.log(checkRows);

            if(checkRows.length == 0 ){
                alert('수정할 코드정보를 선택해 주십시오.');
                return false;
            }

            $.ajax({
                method: 'post',
                url: '/store/standard/std51/' + code + '/mod',
                data: {data:JSON.stringify(checkRows)},
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
                        Search();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(res.msg);
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
        }

        /**
         * @return {boolean}
         */
        function DataDel(){
            let code_ids = [];
            gx.getSelectedRows().forEach((selectedRow, index) => {
                code_ids.push(selectedRow.code_id);
            });

            if(code_ids.length === 0) {
                alert('삭제할 상품을 선택 해 주십시오.');
            } else if(code_ids.length > 0 && confirm('삭제 하시겠습니까?')){

                $.ajax({
                    method: 'post',
                    url: '/store/standard/std51/' + code + '/del',
                    data: {'code_ids':code_ids},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            Search();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;

        }

        /**
         * @return {boolean}
         */
        function ChangeSeq(){
            let code_ids = [];
            gx.gridOptions.api.forEachNode(function(node) {
                code_ids.push(node.data.code_id);
            });
            if(confirm('순서를 변경 하시겠습니까?')){
                $.ajax({
                    method: 'post',
                    url: '/store/standard/std51/' + code + '/seq',
                    data: {'code_ids':code_ids},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            Search();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;
        }

        // 사용여부 변경
        function ChangeYN(){
            let rows = gx.getSelectedRows();

            if(confirm('사용여부를 변경 하시겠습니까?')){
                $.ajax({
                    method: 'post',
                    url: '/store/standard/std51/' + code + '/change-yn',
                    data: {
                        'rows' : rows
                    },
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == 200){
                            alert('선택한 코드의 사용여부가 변경되었습니다.');
                            Search();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }
            return true;
        }
    </script>
@stop
