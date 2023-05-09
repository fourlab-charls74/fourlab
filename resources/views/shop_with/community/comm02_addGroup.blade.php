@extends('shop_with.layouts.layout-nav')
@section('title', '그룹 추가')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">그룹추가</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 알림</span>
                </div>
            </div>
            <div style="float:right">
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="Save();"> 저장</button>
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="window.close()"> 닫기</button>
            </div>
        </div>
        <form name="group">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">그룹상세</a>
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
                                                        <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple ></select>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                            <h6 class="m-0 font-weight-bold">총 <span id="cntStore" class="text-primary">0</span> 건</h6>
                        </div>
                        <div style="float:right;">
                            <button type="button" onclick="addRow()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" id="add_store"> 추가</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd-group" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>

        //매장 다중 선택
        $( ".sch-store" ).on("click", function() {
                    searchStore.Open(null, "multiple");
        });

        let group_columns = [
            {headerName: "매장코드", field: "store_cd", width: 70, cellStyle: {'text-align':'center'}},
            {headerName: "매장명", field: "store_nm",width:"auto"},
            {headerName: "삭제", width:60, cellStyle: {'text-align':'center'},
                cellRenderer: function(params) {
                    return `<a href='#' onclick="del_store_data('${params.data.store_cd}')">삭제</a>`;
                },
            },
            {headerName: "인덱스", field: "group_cd",hide:true},
            {width: "auto"},
        ];

        let gx;
        let del_GroupData = [];
        let del_StoreData = [];
        let cur_group_cd = "";

        const pApp = new App('', { gridId: "#div-gd-group" });

        $(document).ready(function() {
            pApp.ResizeGrid(350);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, group_columns, {
                onCellValueChanged: (e) => {
                e.node.setSelected(true);
                
            }
            });
            gx.Request('/shop/community/comm02/search_group2');

        });

        //매장 선택 후 추가 클릭 시 그리드에 반영
        
        let newData = [];
        let storeData = [];
        let total = [];

        function addRow(){
            newData.length = 0;
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
            
            total.push(
                store_cds.length
            )
             
            let sum = total.reduce((a,b) => (a+b));
            gx.gridOptions.api.applyTransaction({add:newData});
            document.getElementById('cntStore').innerText = sum;
            $('#store_no').val(null).trigger('change');

            // 그리드의 모든 행의 매장코드를 가져와서 같은 매장 코드가 있는지 비교하고 있으면 알림창으로 에러 출력
       }

       // 그룹 저장
       function Save() {
            if ($('input[name="group_nm"]').val() === '') {
                $('input[name="group_nm"]').focus();
                alert('그룹명을 입력해 주세요.');
                return false;
            }
            
            let saveGroupData = [];
                gx.gridOptions.api.forEachNode((obj,idx)=>{
                    saveGroupData.push( obj.data.store_cd);
            });
        
            let frm = $('form[name=group]').serialize();
            
            frm += "&store_cd=" + saveGroupData

            $.ajax({
                method: 'post',
                url: '/shop/community/comm02/add_group',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == 200) {
                        alert('그룹추가에 성공하였습니다.');
                        window.close();
                        opener.location.reload();
                    } else if (data.code == 100) {
                        alert('그룹명이 같은 그룹이 있습니다. 다른 그룹명을 입력해주세요.');
                        return false;
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