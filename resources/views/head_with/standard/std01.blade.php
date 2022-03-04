@extends('head_with.layouts.layout')
@section('title','품목')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">품목</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 품목</span>
    </div>
</div>
<form method="get" name="search">
    <input type="hidden" name="c_id" value="{{$admin_id}}">
    <input type="hidden" name="c_name" value="{{$admin_nm}}">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a> -->
                    <a href="#" data-opt_kind_cd="" onclick="openCodePopup('')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>

                    <!-- <a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="far fa-trash-alt fs-12"></i> 삭제</a> -->
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">품목</label>
                            <div class="flax_box">
                                <input type="text" name="opt_kind" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="sch_use_yn_" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="sch_use_yn_">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="sch_use_yn_y" class="custom-control-input" value="y">
                                    <label class="custom-control-label" for="sch_use_yn_y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="sch_use_yn_n" class="custom-control-input" value="n">
                                    <label class="custom-control-label" for="sch_use_yn_n">N</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">상품 세부 정보</h6>
                </div>
                <div class="fr_box">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<!-- <a href="https://bizest.netpx.co.kr/head/webapps/standard/std03.php">품목관리 링크</a> -->
<script language="javascript">
    var columns = [
        // this row shows the row index, doesn't use any data from the row

        {
            headerName: '#',
            width: 35,
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
            cellStyle: {"background":"#F5F7F7"}
        },
        {
            field: "opt_kind_cd",
            headerName: "품목코드",
            cellStyle: StyleGoodsTypeNM,
            editable: true,
            cellRenderer: function(params) {
                        if (params.value !== undefined) {
                            return '<a href="#" onClick="return openCodePopup(\'' + params.data.opt_kind_cd + '\');">' + params.value + '</a>';
                        }
                    }
        },
        {
            field: "opt_kind_nm",
            headerName: "품목명",
            width:150,
            editable: true,
        },
        {
            field: "goods_cnt",
            headerName: "상품갯수",
            width:72,
            editable: true,
        },
        {
            field: "use_yn",
            headerName: "사용여부",
            width:72,
            editable: true,
        },
        {
            field: "regi_date",
            headerName: "등록일시",
            width: 120,
            editable: true,
        },
        {
            field: "upd_date",
            headerName: "수정일시",
            width: 120,
            editable: true,
        },
        {
            field: "",
            headerName: "",
            width: 'auto',
        },
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275); //280
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search(1);
    });

    pApp.BindSearchEnter();

    /*
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
        });
        gx.Request('/partner/cs/cs72/search', data);
    }
    */
</script>
<script type="text/javascript" charset="utf-8">
    var _isloading = false;

    function onscroll(params) {


        if (_isloading === false && params.top > gridDiv.scrollHeight) {

            var rowtotal = gridOptions.api.getDisplayedRowCount();
            // console.log('getLastDisplayedRow : ' + gridOptions.api.getLastDisplayedRow());
            // console.log('rowTotalHeight : ' + rowtotal * 25);
            // console.log('params.top : ' + params.top);

            if (gridOptions.api.getLastDisplayedRow() > 0 && gridOptions.api.getLastDisplayedRow() == rowtotal - 1) {
                // console.log(params);
                Search(0);
            }
            // var rowtotal = gridOptions.api.getDisplayedRowCount();
            // var rowHeight = 25;
            // var rowTotalHeight = rowtotal * gridOptions.rowHeight;
            // if(rowtotal > 0 && params.top > rowTotalHeight && (rowtotal - 1) == gridOptions.api.getLastDisplayedRow()){
            //     console.log('params.top :' + params.top);
            //     console.log('rowTotalHeight :' + rowTotalHeight);
            //     console.log('top : ' + params.top);
            //     console.log('eGridDiv : ' + eGridDiv.scrollHeight);
            //     console.log(gridOptions.api.getDisplayedRowCount());
            //     console.log(gridOptions.api.getFirstDisplayedRow());
            //     console.log(gridOptions.api.getLastDisplayedRow());
            //     _isloading = true;
            //     Search(0);
            // }
        }
    }

    var _page = 1;
    var _total = 0;
    var _grid_loading = false;
    var _code_items = "";
    var columns_arr = {};
    var option_key = {};

    function Search(page) {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/standard/std01/search', data, page);
    }

    // function openCodePopup(a) {
    //     var kind_cd = $(a).attr('data-code');
    //     var frm1 = document.f1;
    //     //console.log("data-code : " + $(a).attr('data-code'))
    //     var go_url = "";
    //     $.ajax({
    //         async: true,
    //         type: 'post',
    //         url: '/head/standard/std01/GetOpt',
    //         data: {
    //             'opt_kind_cd': kind_cd
    //         },
    //         success: function(data) {
    //             //console.log(data);
    //             var res = data.body[0];
    //             console.log(res.regi_date);
    //             var cd_items = data.kind_cd_items;
    //             //console.log(cd_items);
    //             $("#chg_opt_kind_cd").show();

    //             $("#opt_kind_cd").attr("readonly", true);
    //             frm1.opt_kind_cd.value = res.opt_kind_cd;
    //             frm1.opt_kind_nm.value = res.opt_kind_nm;
    //             frm1.memo.value = res.memo;
    //             document.getElementById("admin").innerHTML = res.admin_nm + "(" + res.admin_id + ")";
    //             document.getElementById("regi_date").innerHTML = res.regi_date;
    //             document.getElementById("upd_date").innerHTML = res.upd_date;
    //             frm1.memo.value = res.memo;
    //             if (res.use_yn == "Y") {
    //                 frm1.use_yn[0].checked = true
    //             } else if (res.use_yn == "N") {
    //                 frm1.use_yn[1].checked = true
    //             }
    //             frm1.c.value = "edit";


    //             for (i = 0; i < cd_items.length; i++) {
    //                 $("select#chg_opt_kind_cd").append("<option value='" + cd_items[i].opt_kind_cd + "'>" + cd_items[i].opt_kind_nm + "</option>");
    //             }
    //             //console.log();

    //         },
    //         complete: function() {
    //             _grid_loading = false;
    //         },
    //         error: function(request, status, error) {
    //             console.log("error")
    //         }
    //     });
    // }

    // function openCodePopup(a) {

    //     const opt_kind_cd = $(a).attr('data-opt_kind_cd');
    //     let url = '/head/standard/std01/create';
    //     if (opt_kind_cd !== '') {
    //         url = '/head/standard/std01/' + opt_kind_cd;
    //     }
    //     window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=1024");
    // }

    function openCodePopup(opt_kind_cd){

        if(opt_kind_cd == '')
            var url = '/head/standard/std01/create';
        else
            var url = '/head/standard/std01/show?opt_kind_cd=' + encodeURIComponent(opt_kind_cd);

        var stock = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=568");
    }

    // function Cmder(cmd) {
    //     if (cmd == "add") { // 등록버튼 실행
    //         ResetForm();
    //         EnableAdd(true);
    //         $("#opt_kind_cd").focus();
    //     } else if (cmd == "savecmd") { // 등록모드
    //         var c = $("form[name=f1]>input[name=c]").val();
    //         if (c == "edit") {
    //             if (Validate("editcmd")) {
    //                 SaveCmd("editcmd");
    //             }
    //         } else {
    //             if (Validate("addcmd")) {
    //                 SaveCmd("addcmd");
    //             }
    //         }

    //     } else if (cmd == "delcmd") { // 삭제모드
    //         DeleteCmd(cmd);
    //     } else if (cmd == "grid_load") { // 그리드 출력
    //     }
    // }

    // /*
    //  *	품목 등록, 수정
    //  */
    // function SaveCmd(cmd) {
    //     var f1 = $("form[name=f1]");
    //     var go_url = "";
    //     if (cmd == "addcmd") { //등록
    //     } else { //수정
    //         go_url = "";
    //     }
    //     $.ajax({
    //         async: true,
    //         type: 'post',
    //         url: '/head/standard/std01/Command',
    //         data: f1.serialize() + "&cmd=" + cmd,
    //         success: function(data) {
    //             //console.log(data);
    //             Search(1);

    //         },
    //         complete: function() {
    //             _grid_loading = false;
    //         },
    //         error: function(request, status, error) {
    //             console.log("error")
    //             //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    //         }
    //     });
    //     if (cmd == "editcmd") {
    //         openCodePopup('');
    //     } else {
    //         ResetForm();
    //         EnableAdd(true);
    //     }


    // }

    // /*
    //  *	품목 삭제
    //  */
    // function DeleteCmd(cmd) {
    //     var f1 = $("form[name=f1]");
    //     var selectedRowData = gx.gridOptions.api.getSelectedRows();
    //     var selectOptInfo = selectedRowData[0];

    //     if (selectOptInfo == undefined) {
    //         alert("삭제할 품목을 선택하세요.");
    //         return false;
    //     }

    //     if (selectOptInfo.opt_kind_cd == "none") {
    //         alert("'none' 품목은 삭제 할 수 없습니다.");
    //         return false;
    //     }

    //     if (!confirm("삭제 하시겠습니까?")) {
    //         return false;
    //     }
    //     //console.log(cmd);
    //     $.ajax({
    //         async: true,
    //         type: 'post',
    //         url: '/head/standard/std01/Command',
    //         data: f1.serialize() + "&cmd=" + cmd,
    //         success: function(data) {
    //             //console.log(data);
    //             Search(1);
    //         },
    //         complete: function() {
    //             _grid_loading = false;
    //         },
    //         error: function(request, status, error) {
    //             console.log("error")
    //             //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    //         }
    //     });
    //     EnableAdd(true);
    // }

    // function Validate(cmd) {
    //     if (cmd == "addcmd") {
    //         if ($("input[name=opt_kind_cd]").val() == "") {
    //             alert("품목코드를 입력하십시오.");
    //             $("#opt_kind_cd").focus();
    //             return false;
    //         }
    //         if ($("#opt_kind_cd").val() == "") {
    //             alert("등록된 품목코드 입니다. 사용하실 수 없습니다.");
    //             $("#opt_kind_cd").focus();
    //             return false;
    //         }
    //     }
    //     if ($("input[name=opt_kind_nm]").val() == "") {
    //         alert("품목명을 입력하십시오.");
    //         $("input[name=opt_kind_nm]").focus();
    //         return false;
    //     }
    //     return true;
    // }

    // /*
    //  *	품목 변경 처리
    //  */
    // function ChangeOptKindCode() {
    //     var f2 = document.f1;
    //     var f1 = $("form[name=f1]");

    //     var opt_kind_cd = $("[name=opt_kind_cd]").value;
    //     var chg_opt_kind_cd = f2.chg_opt_kind_cd.value;

    //     if (opt_kind_cd == "none") { // 필수 품목인 none은 삭제 못함.
    //         alert("'none' 품목은 변경할 수 없습니다.");
    //         return false;
    //     }
    //     if (chg_opt_kind_cd == "") {
    //         alert("품목을 선택해 주십시오.");
    //         return false;
    //     }
    //     if (opt_kind_cd == chg_opt_kind_cd) {
    //         alert("같은 품목으로는 변경할 수 없습니다.\n\n다른 품목을 선택해 주십시오.");
    //         return false;
    //     }
    //     if (!confirm("품목정보를 변경하시겠습니까?\n\n해당 품목으로 등록된 상품의 품목도 일괄변경 됩니다.")) {
    //         return false;
    //     }
    //     //console.log(cmd);
    //     $.ajax({
    //         async: true,
    //         type: 'post',
    //         url: '/head/standard/std01/Command',
    //         data: f1.serialize() + "&cmd=chg_opt_kind",
    //         success: function(data) {
    //             //console.log(data);
    //             Search(1);
    //         },
    //         complete: function() {
    //             _grid_loading = false;
    //         },
    //         error: function(request, status, error) {
    //             console.log("error")
    //             //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    //         }
    //     });
    //     ResetForm();
    //     EnableAdd(true);
    //     /*
    //     var param = formData2QueryString(document.f2);
    //     param += "&CMD=chg_opt_kind";
    //     param += "&UID=" + Math.random();

    //     var http = new xmlHttp();
    //     http.onexec('std03.php','POST',param,true,cbChangeOptKindCode);

    //     ProcessingPopupShowHide("show");
    //     */
    // }

    // function cbChangeOptKindCode(res) {
    //     if (res.opt_in_result == "200") {
    //         //ProcessingPopupShowHide();
    //         ResetForm();
    //         EnableAdd(true);
    //         //GridListDraw(false);
    //         Search(1);
    //     } else {
    //         alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
    //     }
    // }

    // /*
    //  *	품목 코드 중복 및 한글 포함 여부 판단
    //  */
    // var i = 0;

    // function CheckOpt() {
    //     var opt_kind_cd = $("#opt_kind_cd").val();
    //     if (opt_kind_cd) {

    //         // 한글 포함 여부 판단
    //         var opt_kind_cd = opt_kind_cd ? opt_kind_cd : el.opt_kind_cd;
    //         var pattern = /^[a-zA-Z0-9]+$/;

    //         if (!pattern.test(opt_kind_cd)) {
    //             /*
    //             alert("영문 또는 숫자만 입력 가능합니다.!!");
    //             $("#opt_kind_cd").val("");
    //             // 품목코드 중복 메세지 초기화
    //             $("#CheckOptMessage").html('');
    //             return false;
    //             */
    //             $("#opt_kind_cd").val("");
    //             // 품목코드 중복 메세지 초기화
    //             $("#CheckOptMessage").html('');

    //         }

    //         $.ajax({
    //             async: true,
    //             type: 'post',
    //             url: '/head/standard/std01/CheckOpt',
    //             data: "opt_kind_cd=" + opt_kind_cd,
    //             success: function(data) {
    //                 //console.log(data);
    //                 cbCheckOpt(data);
    //             },
    //             complete: function() {
    //                 _grid_loading = false;
    //             },
    //             error: function(request, status, error) {
    //                 console.log("error")
    //                 console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
    //             }
    //         });

    //     }
    // }

    // function cbCheckOpt(res) {
    //     if (res.responseText == "1") {
    //         $("input[name=opt_check]").val("Y");
    //         $("#CheckOptMessage").html('');
    //     } else {
    //         $("input[name=opt_check]").val("");
    //         $("#CheckOptMessage").css({
    //             "color": 'red'
    //         });
    //         $("#CheckOptMessage").html("등록된 품목코드 입니다. 사용하실 수 없습니다.");
    //     }
    // }

    // function EnableAdd(flag) {
    //     if (flag) {
    //         $("#chg_opt_kind_cd").hide();
    //         $("#CheckOptMessage").show();
    //         $("input[name=opt_kind_cd]").attr("readOnly", false).css({
    //             'background': '#fff'
    //         });
    //         $("[name=c]").val('');
    //         $("span#admin").html($("[name=c_name]").val() + "(" + $("[name=c_id]").val() + ")"); //로그인 정보로 등록자 세팅.
    //         //document.getElementById("admin").innerHtml = $("c_name").val() + "(" + $("c_id").val() + ")";
    //     } else {
    //         $("#chg_opt_kind_cd").show();
    //         $("#CheckOptMessage").hide();
    //         $("input[name=opt_kind_cd]").attr("readOnly", true);
    //         $("[name=c]").value("edit");
    //     }
    // }

    // function ResetForm() {
    //     var f1 = document.f1;
    //     f1.reset();

    //     f1.use_yn[0].checked = true;

    //     document.getElementById("admin").innerHTML = "";
    //     document.getElementById("regi_date").innerHTML = "";
    //     document.getElementById("upd_date").innerHTML = "";
    // }
</script>
@stop
