@extends('store_with.layouts.layout')
@section('title','판매채널관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">판매채널관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보관리</span>
        <span>/ 판매채널관리</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매채널명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='store_channel' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_yn" class="custom-control-input" value="">
                                    <label class="custom-control-label" for="use_yn">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_y" class="custom-control-input" checked="" value="y">
                                    <label class="custom-control-label" for="use_y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="n">
                                    <label class="custom-control-label" for="use_n">N</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i>등록</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

<div class="row show_layout">
    <div class="col-lg-4 pr-1">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 pt-1 pb-1 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0">판매채널</h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2" onclick="changeSeqStoreChannel()" > 판매채널 순서변경</a>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm pl-2 ml-1" onclick="openAddPopup()" ><i class="bx bx-plus fs-16"></i> 판매채널 등록</a>
                    <!-- <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="deleteStoreChannel()"><i class="fas fa-trash-alt fa-sm"></i> 삭제</button> -->
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-0"><span id="select_store_nm"></span>매장구분</h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2" onclick="changeSeqStoreType()" > 매장구분 순서변경</a>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm pl-2 ml-1" onclick="openAddTypePopup()" ><i class="bx bx-plus fs-16"></i> 매장구분 등록</a>
                    <!-- <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="deleteStoreType()"><i class="fas fa-trash-alt fa-sm"></i> 삭제</button> -->
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-type" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
        {headerName: "No", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 60, cellStyle: {"text-align": "center"}, rowDrag: true},
        
        {field: "store_channel_cd", headerName: "판매채널코드", width: 110, cellStyle:{'text-align' : 'center'},
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='openEditPopup("${params.data.store_channel_cd}", "${params.data.store_type}" , "${params.data.idx}")'>${params.value}</a>`;
            }
        },
        {field: "store_channel", headerName: "판매채널", width: 100, cellStyle:{'text-align' : 'center'},
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='SearchDetail("${params.data.store_channel_cd}", "${params.value}")'>${params.value}</a>`;
            }
        },
        {field: "use_yn", headerName: "사용여부", cellStyle:{'text-align' : 'center'},},
        {width: 0}
        
    ]

    const store_type_columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
        {headerName: "No", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 60, cellStyle: {"text-align": "center"}, rowDrag: true},
        {field: "store_kind_cd", headerName: "매장구분코드", width: 100, cellStyle:{'text-align' : 'center'},
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='openEditPopup("${params.data.store_kind_cd}", "${params.data.store_type}", "${params.data.idx}")'>${params.value}</a>`;
            }
        },
        {field: "store_kind", headerName: "매장구분", width: 100, cellStyle:{'text-align' : 'center'},},
        {field: "use_yn", headerName: "사용여부", cellStyle:{'text-align' : 'center'},},
        {width: 0}
    ]

</script>
<script type="text/javascript" charset="utf-8">
    let gx, gx2;

    const pApp = new App('', { gridId: "#div-gd", height: 284 });
    const pApp2 = new App('', { gridId: "#div-gd-type" });

    $(document).ready(function() {
        // 매장목록
        pApp.ResizeGrid(284);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        gx.gridOptions.rowDragManaged = true;
        gx.gridOptions.animateRows = true;

        // 동종업계 세부정보
        pApp2.ResizeGrid(275);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, store_type_columns);
        gx2.gridOptions.rowDragManaged = true;
        gx2.gridOptions.animateRows = true;

        // 최초검색
        Search();

        // 검색조건 숨김 시 우측 grid 높이 설정
        $(".search_mode_wrap .dropdown-menu a").on("click", function(e) {
            if(pApp2.options.grid_resize == true){
                pApp2.ResizeGrid(275);
            }
        });
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/standard/std09/search', data, -1, function(e){
            // if (e.body.length > 0) {
            //     SearchDetail(e.body[0].store_channel_cd, e.body[0].store_channel);
            // }
        });
    }

    function SearchDetail(store_channel_cd, store_channel) {
        if(store_channel_cd === '') return;
        gx2.Request("/store/standard/std09/search-store-type/" + store_channel_cd, "", -1, function(e){
            $("#select_store_nm").text(`${store_channel} - `);
        });
    }

    function openAddPopup() {
        const url = '/store/standard/std09/show';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=420");
    }

    function openAddTypePopup () {
        const url = '/store/standard/std09/add';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=480");
    }

    function openEditPopup(code, type, idx) {
        const url = '/store/standard/std09/show/' + code + '/' + type + '/' + idx;
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=420");
    }

    function changeSeqStoreChannel(){
        let store_channel_cds = [];
        gx.gridOptions.api.forEachNode(function(node) {
            store_channel_cds.push(node.data.store_channel_cd);
        });

        if(confirm('판매채널 순서를 변경 하시겠습니까?')){
            $.ajax({
                method: 'post',
                url: '/store/standard/std09/change-seq-store-channel',
                data: {'store_channel_cds': store_channel_cds},
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
                        alert(res.msg);
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

    function changeSeqStoreType(){
        let store_types = [];
        gx2.gridOptions.api.forEachNode(function(node) {
            store_types.push(node.data.store_kind_cd);
        });

        if(confirm('매장구분 순서를 변경 하시겠습니까?')){
            $.ajax({
                method: 'post',
                url: '/store/standard/std09/change-seq-store-type',
                data: {'store_types': store_types},
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
                        alert(res.msg);
                        SearchDetail(res.store_channel_cd, res.store_channel);
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


    // 판매채널, 매장구분 삭제 일단 주석처리 추후 필요할 수 있음
    // //매장구분 삭제
    // function deleteStoreType() {
    //     let rows = gx2.getSelectedRows();

    //     if (rows.length == 0) {
    //         alert('삭제할 매장구분을 선택해주세요');
    //         return false;
    //     }

    //     if (confirm('선택한 매장구분을 삭제하시겠습니까?') === false) return;

    //     axios({
    //         url: '/store/standard/std09/delete',
    //         method: 'post',
    //         data: { 
    //             data: rows.map(r => ({ store_channel_cd : r.store_channel_cd, store_kind_cd : r.store_kind_cd })),
    //         },
    //     }).then(function (res) {
    //         if(res.data.code === 200) {
    //             console.log(res);
    //             alert("매장구분이 삭제되었습니다.");
    //             SearchDetail(res.data.store_channel_cd, res.data.store_channel);
    //         } else {
    //             console.log(res.data);
    //             alert("매장구분 삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
    //         }
    //     }).catch(function (err) {
    //         console.log(err);
    //     });
    // }

    // //판매채널 삭제
    // function deleteStoreChannel() {
    //     let rows = gx.getSelectedRows();

    //     if (rows.length == 0) {
    //         alert('삭제할 판매채널을 선택해주세요');
    //         return false;
    //     }

    //     if (confirm('선택한 판매채널의 매장구분도 삭제됩니다. 삭제하시겠습니까?') === false) return;

    //     axios({
    //         url: '/store/standard/std09/delete-channel',
    //         method: 'post',
    //         data: { 
    //             data: rows.map(r => ({ store_channel_cd : r.store_channel_cd})),
    //         },
    //     }).then(function (res) {
    //         if(res.data.code === 200) {
    //             alert("판매채널이 삭제되었습니다.");
    //             Search();
    //             SearchDetail();
    //         } else {
    //             console.log(res.data);
    //             alert("판매채널 삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
    //         }
    //     }).catch(function (err) {
    //         console.log(err);
    //     });
    // }
</script>
@stop
