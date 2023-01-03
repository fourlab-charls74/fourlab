@extends('store_with.layouts.layout-nav')
@section('title','그룹관리')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">그룹관리</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 알림</span>
                </div>
            </div>
            <div style="float:right;">
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="openGroupAdd();"> 새그룹 추가</button>
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="Save();">저장</button>
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="window.close()"> 닫기</button>
            </div>
        </div>
        <form method="post" name="group">
            <div class="row show_layout">
                <div class="col-sm-5 pr-1">
                    <div class="card shadow-none mb-0">
                        <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                            <h5 class="m-0">그룹</h5>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="delGroupData();">그룹삭제</button>
                        </div>
                        <div class="card-body shadow pt-2">
                        <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 pr-1">
                    <div class="card shadow">
                        <div class="card-header mb-0">
                            <a href="#">그룹 수정</a>
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
                                                        <th>그룹명</th>
                                                        <td>
                                                            <div class="input_box">
                                                                <input type='text' class="form-control form-control-sm search-all" name='group_nm' id='group_nm' value=''>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>매장</th>
                                                        <td>
                                                            <div class="form-inline inline_btn_box" style="display:inline-block; width:100%">
                                                                <select id="store_no" name="store_no[]" id="store_no" class="form-control form-control-sm select2-store multi_select" multiple ></select>
                                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr style="display: none;">
                                                        <th>그룹코드</th>
                                                        <td>
                                                            <div class="form-inline inline_btn_box" style="display:inline-block; width:100%">
                                                                <input type='text' class="form-control form-control-sm search-all" name='group_cd' id='group_cd' value=''>
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
                        <div class="card-body shadow pt-2">
                            <div class="filter_wrap" style="margin:10px;">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-group-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div style="float:right;">
                                    <button type="button" onclick="addRow()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" id="add_store"> 추가</button>
                                    <!-- <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="delStoreData();">매장삭제</button> -->
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-group" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script language="javascript">
        let columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
            {headerName: "그룹명", field: "group_nm",width:120,
                cellRenderer: function(params) {
                    return `<a href='javascript:void(0)' onclick='SearchDetail("${params.value}", "${params.data.group_nm}", "${params.data.group_cd}")'>${params.value}</a>`;
                }
            },
            {headerName: "그룹코드", field: "group_cd", hide: true},
            {width: "auto"}
        ];

        let group_columns = [
            // {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
            {headerName: "매장코드", field: "store_cd", width: 70, cellStyle: {'text-align':'center'}},
            {headerName: "매장명", field: "store_nm",width:"auto"},
            {headerName: "삭제", width:60, cellStyle: {'text-align':'center'},
                cellRenderer: function(params) {
                    return `<a href='#' onclick="del_store_data('${params.data.store_cd}')">삭제</a>`;
                },
            },
            {headerName: "그룹코드", field: "group_cd", hide:true},
            {width: "auto"},
        ];


        let gx, gx2;
        let del_GroupData = [];
        let del_StoreData = [];
        let cur_group_cd = "";

        const pApp = new App('', { gridId: "#div-gd" });
        const pApp2 = new App('', { gridId: "#div-gd-group" });

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            gx.Request('/store/stock/stk32/search_group');

            pApp2.ResizeGrid(405);
            pApp2.BindSearchEnter();
            let gridDiv2 = document.querySelector(pApp2.options.gridId);
            gx2 = new HDGrid(gridDiv2, group_columns, {
                onCellValueChanged: (e) => {
                e.node.setSelected(true);
                
            }
            });
            gx2.Request('/store/stock/stk32/search_group2');

        });

        $(document).ready(function() {
            $('#group_nm').attr('disabled', true);
            $('#store_no').attr('disabled', true);
        });
     

        function SearchDetail(group_nm, store_cd, group_cd) {
            if(store_cd === '') return;

            $('#group_nm').attr('disabled', false);
            $('#store_no').attr('disabled', false);

            $('#group_nm').val(group_nm);
            $('#group_cd').val(group_cd);
            cur_group_nm = group_nm;
            cur_store_cd = store_cd;
            cur_group_cd = group_cd;

            gx2.Request('/store/stock/stk32/search_group2','group_cd='+ group_cd);
        }

        //매장 다중 선택
        $( ".sch-store" ).on("click", function() {
            searchStore.Open(null, "multiple");
        });

        
        let newData = [];
        let storeData = [];
        let total = [];

        function addRow(){
            newData.length = 0;
            total.length = 0;

            let select_row = document.getElementById('store_no').selectedIndex;

            if(select_row == -1) {
                return alert('매장을 하나라도 선택 후 버튼을 클릭해주세요')
            }

            let store_cds = [];
            let store_nms = [];
            for (let sel_option of document.getElementById('store_no').options) {
                if (sel_option.selected) {
                    store_cds.push(sel_option.value);
                    store_nms.push(sel_option.innerText);
                }
            }

            for (let i = 0;i<store_cds.length;i++) {
                newData.push({
                    store_cd:store_cds[i],
                    store_nm: store_nms[i]
                })
                storeData.push({
                    store_cd:store_cds[i],
                    store_nm: store_nms[i]
                })
            }
            let gd_cnt = Number(document.getElementById('gd-group-total').innerText);


            total.push(
                store_cds.length
            )
             
            sum = total.reduce((a,b) => (a+b));
            let total_cnt = sum + gd_cnt;
            gx2.gridOptions.api.applyTransaction({add:newData});
            document.getElementById('gd-group-total').innerText = total_cnt;
            $('#store_no').val(null).trigger('change');

            // 그리드의 모든 행의 매장코드를 가져와서 같은 매장 코드가 있는지 비교하고 있으면 알림창으로 에러 출력
       }


        //그룹관리에서 그룹데이터 삭제
        function delGroupData() {
            const rows = gx.getSelectedRows();

            if (rows.length == 0) {
                return alert('삭제할 그룹을 적어도 하나는 선택해주세요');
            }

            if(confirm('그룹을 삭제하면 매장목록도 같이 삭제됩니다.\n그래도 삭제하시겠습니까?')) {
                $.ajax({
                    method: 'post',
                    url: '/store/stock/stk32/del_group',
                    data: {rows : rows},
                    dataType: 'json',
                    success: function(data) {
                        if (data.code == '200') {
                            alert('그룹삭제에 성공하였습니다.');
                            location.reload();
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
       
        function del_store_data(store_cd) {
           let rows = gx2.getRows();
           let selectedData = rows.filter(r => r.store_cd === store_cd);
           if(selectedData.length > 0) selectedData = selectedData[0];
           else selectedData = {};

           del_StoreData.push(selectedData['store_cd']);
           gx2.gridOptions.api.applyTransaction({ remove: [selectedData] });
       }
        
        //저장
        function Save() {
            if ($('input[name="group_nm"]').val() === '') {
                $('input[name="group_nm"]').focus();
                alert('그룹명을 입력해 주세요.');
                return false;
            }
            
            let saveGroupData = [];
                gx2.gridOptions.api.forEachNode((obj,idx)=>{
                    saveGroupData.push( obj.data.store_cd);
            });

            let group_cd = $('#group_cd').val();

            let add_newData = [];
            for(let i = 0; i < newData.length; i++) {
                    add_newData.push(newData[i].store_cd);
            }

            let frm = $('form[name=group]').serialize();
            frm += "&store_cd=" + saveGroupData;
            frm += "&group_cd=" + group_cd;
            frm += "&add_store=" + add_newData.join(',');
            frm += "&del_data=" + del_StoreData.join(',');

            console.log(frm);
            $.ajax({
                method: 'post',
                url: '/store/stock/stk32/update',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == 200) {
                        alert('그룹수정에 성공하였습니다.');
                        location.reload();
                        opener.location.reload();
                    } else {
                        alert('이미 같은 매장이 그룹에 포함되어 있습니다. \n한 그룹에 같은 매장은 추가할 수 없습니다.');
                    }
                },
                error: function(e) {
                        console.log(e.responseText)
                }
            });
        }

        //그룹 추가 팝업
        function openGroupAdd() {
            const url = '/store/stock/stk32/addGroup';
            const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=800");
        }
    </script>
@stop
