@extends('store_with.layouts.layout-nav')
@section('title','그룹관리')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">알림</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 그룹관리</span>
                </div>
            </div>
            <div style="float:right;">
               
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="window.close()"> 닫기</button>
            </div>
        </div>
        <form method="post" name="add_group">
            <div class="row show_layout">
                <div class="col-sm-5 pr-1">
                    <div class="card shadow-none mb-0">
                        <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                            <h5 class="m-0">그룹</h5>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" id="add_group_data"> 추가</button>
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
                    <div class="card shadow-none mb-0">
                        <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                            <h5 class="m-0">매장목록</h5>
                                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="saveGroupData()" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="modGroupData()" id="modData">수정</button>
                        </div>
                        
                        <div class="card-body shadow pt-2">
                            <div class="form-group" id="addInput">
                                <div class="flax_box" style="display:inline-block">
                                    <label for="">그룹명</label>&nbsp;&nbsp;&nbsp;
                                    <input type='text' class="form-control form-control-sm" name='group_nm' id="group_nm" value='' style="width:85%;">
                                </div>
                            </div>
                            <div class="form-inline inline_btn_box" style="display:inline-block; width:85%">
                                <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple ></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>&nbsp;
                            
                            <div style="display:inline-block">
                                <button type="button" onclick="addRow()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" id="add_store"> 추가</button>
                            </div><br><br>

                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="total" class="text-primary">0</span> 건</h6>
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
            {headerName: "그룹명", field: "group_nm",width:120,
                cellRenderer: function(params) {
                    // console.log(params.data.group_cd);
                    return `<a href='javascript:void(0)' onclick='SearchDetail("${params.value}", "${params.data.group_nm}", "${params.data.group_cd}")'>${params.value}</a>`;
                }
            },
            {headerName: "삭제",width:60, 
                cellRenderer: function(params) {
                    return `<a href='#' onclick="del_group_data('${params.data.group_cd}')">삭제</a>`;
                },
                cellStyle: params => {
                    return {textAlign:'center'}
            }
            },
            {headerName: "인덱스", field: "group_cd",hide:true},
            {width: "auto"}
        ];

        let group_columns = [
            {headerName: "매장코드", field: "store_cd",width:70,
                cellStyle: params => {
                    return {textAlign:'center'}
                }
            },
            {headerName: "매장명", field: "store_nm",width:"auto"},
            {headerName: "삭제",width:60, 
                cellRenderer: function(params) {
                    // console.log(params.rowIndex);
                    return `<a href='#' onclick="del_store_data('${params.data.store_cd}')">삭제</a>`;
                },
                cellStyle: params => {
                    return {textAlign:'center'}
                }
            },
            {headerName: "인덱스", field: "group_cd",hide:true},
            {width: "auto"},
        ];
    </script>

    <script>
        let gx, gx2;
        let del_GroupData = [];
        let del_StoreData = [];
        let cur_group_cd = "";
        let newData = [];

        const pApp = new App('', { gridId: "#div-gd" });
        const pApp2 = new App('', { gridId: "#div-gd-group" });

        $(document).ready(function() {
            pApp.ResizeGrid(200);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            gx.Request('/store/stock/stk32/search_group');

            pApp2.ResizeGrid(200);
            pApp2.BindSearchEnter();
            let gridDiv2 = document.querySelector(pApp2.options.gridId);
            gx2 = new HDGrid(gridDiv2, group_columns);
            gx2.Request('/store/stock/stk32/search_group2');

        });
        $(document).ready(function(){
            $('#saveData').show();
            $('#modData').hide();
        })
        function SearchDetail(group_nm, store_cd, group_cd) {
            $('#saveData').hide();
            $('#modData').show();
            // delData = "";
            if(store_cd === '') return;
            console.log(group_nm);
        
            $('#group_nm').val(group_nm);
            cur_group_nm = group_nm;
            cur_store_cd = store_cd;
            cur_group_cd = group_cd;

            gx2.Request('/store/stock/stk32/search_group2','group_cd='+ group_cd);
        }

       $(document).ready(function() {
            $('#add_group_data').click(function() {
                $('#saveData').show();
                $('#modData').hide();
                document.getElementById('group_nm').value ='';
                gx2.gridOptions.api.setRowData([]);
            });
            
        });

        function addRow(){
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
            }
            console.log(newData);

            gx2.gridOptions.api.applyTransaction({add:newData});
            $('#store_no').val(null).trigger('change');
       }

       // 그룹삭제
        function del_group_data(group_cd) {

            console.log(group_cd);
            
            if(confirm('그룹을 삭제하면 매장목록도 같이 삭제됩니다.\n그래도 삭제하시겠습니까?')) {
                $.ajax({
                    method: 'post',
                    url: '/store/stock/stk32/del_group',
                    data: {group_cd : group_cd},
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
       
       //매장 목록 삭제
        function del_store_data(store_cd) {
            let rows = gx2.getRows();
            let selectedData = rows.filter(r => r.store_cd === store_cd);
            if(selectedData.length > 0) selectedData = selectedData[0];
            else selectedData = {};

            del_StoreData.push(selectedData);
            gx2.gridOptions.api.applyTransaction({ remove: [selectedData] });
        }

       //저장
       function saveGroupData() {
            
            let saveData = [];
                gx2.gridOptions.api.forEachNode((obj,idx)=>{
                    // console.log(obj);
                    saveData.push( obj.data.store_cd);
            });

            // console.log(saveData);
        
            let frm = $('form[name=add_group]').serialize();
            
            frm += "&store_cd=" + saveData
            
            console.log(frm);


            if ($('input[name="group_nm"]').val() === '') {
                $('input[name="group_nm"]').focus();
                alert('그룹명을 입력해 주세요.');
                return false;
            }


            $.ajax({
                method: 'post',
                url: '/store/stock/stk32/add_group',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == '200') {
                        alert('그룹추가에 성공하였습니다.');
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

        //수정
        function modGroupData(no) 
        {
            //매장목록 삭제
            let del_arr = [];
            for(let i = 0; i < del_StoreData.length; i++) {
                del_arr.push(del_StoreData[i].store_cd);
            }

            //매장목록 인덱스값
            let del_arr_index = [];
            for(let i = 0; i < del_StoreData.length; i++) {
                del_arr_index.push(del_StoreData[i].group_cd);
            }
            
            //그룹 삭제
            // let del_group_arr = [];
            // for(let i = 0; i < del_GroupData.length; i++) {
            //     del_group_arr.push(del_GroupData[i].group_cd);
            // }

            //매장 추가
           let add_newData = [];
           for(let i = 0; i < newData.length; i++) {
                add_newData.push(newData[i].store_cd);
           }

        //    console.log(add_newData);

            let frm = $('form[name=add_group]').serialize();
            frm += "&store_cd=" + del_arr.join(',');
            frm += "&group_cd=" + cur_group_cd;
            // frm += "&del_group_cd=" + del_group_arr.join(',');
            frm += "&add_store=" + add_newData.join(',');
            
            console.log(frm);

            $.ajax({
                method: 'post',
                url: '/store/stock/stk32/mod_group',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == '200') {
                        alert('그룹 수정에 성공하였습니다.');
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

        
       
       $( ".sch-store" ).on("click", function() {
            searchStore.Open(null, "multiple");
        });

    </script>


@stop
