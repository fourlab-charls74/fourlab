@extends('head_with.layouts.layout')
@section('title','템플릿')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">템플릿</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 템플릿</span>
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
                    <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="far fa-trash-alt fs-12"></i> 삭제</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">템플릿 구분</label>
                            <div class="flax_box">
                                <select name="tpl_kind" id="tpl_kind" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($tpl_kind_items as $tpl_kind)
                                        <option value='{{ $tpl_kind->id }}'>{{ $tpl_kind->val }}</option>
                                    @endforeach
                                </select>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>템플릿 유형</label>
                            <div class="flax_box">
                                <select name="qna_type" id="qna_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($qna_type_items as $qna_type)
                                        <option value='{{ $qna_type->id }}'>{{ $qna_type->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">제목</label>
                            <div class="flax_box">
                                <input type="text" name="subject" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="far fa-trash-alt fs-12"></i> 삭제</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>


<div class="show_layout">
    
    <form name="f1" id="f1">
        <input type="hidden" name="c">
        <input type="hidden" name="qna_no" value="">
        <input type="hidden" name="cs_id" value="{$admin_id}">
        <input type="hidden" name="cs_nm" value="{$admin_nm}">
        <div class="row">
            <div class="col-sm-6">
                <div class="card_wrap h-100">
                    <div class="card shadow h-100">
                        <div class="card-header mb-0">
                            <h5 class="m-0 font-weight-bold">상품 세부 정보</h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="table-responsive">
                                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card_wrap h-100">
                    <div class="card shadow h-100">
                        <div class="row_wrap">
                            <div class="card-header mb-0">
                                <h5 class="m-0 font-weight-bold">템플릿 정보</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>제목</th>
                                                <td>
                                                    <div class="flax-box">
                                                        <input type="text" name="subject" id="subject" class="form-control form-control-sm search-all">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>템플릿 구분</th>
                                                <td>
                                                    <div class="flax-box">
                                                        <select class="form-control form-control-sm" name="tpl_kind_view">
                                                            <option value=''>선택해 주십시오.</option>
                                                            @foreach ($tpl_kind_items as $tpl_kind)
                                                                <option value='{{ $tpl_kind->id }}'>{{ $tpl_kind->val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>템플릿 유형</th>
                                                <td>
                                                    <div class="input_box">
                                                        <select class="form-control form-control-sm" name="qna_type">
                                                        <option value=''>선택해 주십시오.</option>
                                                            @foreach ($qna_type_items as $qna_type)
                                                                <option value='{{ $qna_type->id }}'>{{ $qna_type->val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>메모</th>
                                                <td>
                                                    <div class="area_box">
                                                        <textarea class="form-control" name="content" style="height:100px;"></textarea>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    <div class="input_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" checked>
                                                                <label class="custom-control-label" for="use_yn_y">Y</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N">
                                                                <label class="custom-control-label" for="use_yn_n">N</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>등록자</th>
                                                <td>
                                                    <span id="admin">{{$admin_nm}}({{$admin_id}})</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>등록일시</th>
                                                <td>
                                                    <span id="regi_date"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>수정일시</th>
                                                <td>
                                                    <span id="upd_date"></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- 확인 -->
                        <div style="text-align:center;" class="mt-2">
                            <input type="button" class="btn btn-sm btn-primary shadow-sm" value="확인" onclick="Cmder('savecmd')">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script language="javascript">
var columns = [
        // this row shows the row index, doesn't use any data from the row
       
        {
            headerName: '#',
            width:35,
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
            cellStyle: {"background":"#F5F7F7"}
        },
        {field:"kind",headerName:"템플릿 구분",width:100,cellStyle:StyleGoodsTypeNM,editable: true, },
        {field:"tplkind",headerName:"템플릿 유형",width:100,editable: true, },
        {field:"subject",headerName:"제목", width:150, editable: true, 
            cellRenderer:function(params){
                return '<a href="javascript:;" data-code="'+params.data.qna_no+'" onClick="GetInfo(this)">'+ params.value+'</a>'
            }
        },
        {field:"use_yn",headerName:"사용여부", editable: true,},
        {field:"qna_no", headerName:"qna_no", hide:true,},
        {field:"", headerName:"", width:"auto"}
];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275); //280
        pApp.BindSearchEnter();
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
    function onscroll(params){


        if(_isloading === false && params.top > gridDiv.scrollHeight){

            var rowtotal = gridOptions.api.getDisplayedRowCount();
            // console.log('getLastDisplayedRow : ' + gridOptions.api.getLastDisplayedRow());
            // console.log('rowTotalHeight : ' + rowtotal * 25);
            // console.log('params.top : ' + params.top);

            if(gridOptions.api.getLastDisplayedRow() > 0 && gridOptions.api.getLastDisplayedRow() ==  rowtotal -1) {
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
        gx.Request('/head/standard/std20/search', data, page);
    }

    function Cmder(cmd){
        event.preventDefault();
        if(cmd == "add"){	// 등록버튼 실행
            ResetForm();
            EnableAdd(true);
            $("#opt_kind_cd").focus();
        } else if(cmd == "savecmd"){		// 등록모드
            var c = $("form[name=f1]>input[name=c]").val();
            if(c == "edit"){
                if(Validate("editcmd")){
                    SaveCmd("editcmd");
                }
            }else{
                if(Validate("addcmd")){
                    SaveCmd("addcmd");
                }
            }
            
        }else if(cmd == "delcmd") {	//삭제

            if( $("[name=qna_no]").val() == "" ){
                alert("삭제할 템플릿을 선택해 주십시오.");
            } else{
                if(confirm("정말 삭제 하시겠습니까?")){
                    SaveCmd("delcmd");
                    ResetForm();
                    EnableAdd(true);
                }
            }

        }
    }

    /*
    *	템플릿 등록, 수정, 삭제
    */
    function SaveCmd( cmd ){
        var frm1 = $("#f1");
        
        $.ajax({
            async: true,
            type: 'post',
            url: '/head/standard/std20/Command',
            data: frm1.serialize() +"&cmd="+ cmd,
            success: function (data) {
                Search(1);
                if(cmd === 'addcmd') {
                    ResetForm();
                    EnableAdd(true);
                }
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error")
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

    }

    function cbSaveCmd(res){
        if(res.responseText == "1"){	//등록
            GridListDraw();
            FormReset(document.f2);
        }else if(res.responseText == "2"){	//수정
            GridListDraw();
        }else if(res.responseText == "3"){	//삭제
            GridListDraw();
            FormReset(document.f2);
        } else{
            alert("오류가 발생했습니다.관리자에게 문의하시기 바랍니다.");
        }
    }



    /*
    *	템플릿 정보 얻기
    */
    function GetInfo(a){
        var qna_no = $(a).attr('data-code');
        var frm1 = document.f1;
        var go_url = "";

        $.ajax({
            async: true,
            type: 'post',
            url: '/head/standard/std20/GetInfo',
            data: {
                'qna_no' : qna_no
            },
            success: function (data) {
                //console.log(data);
                var res = data.body[0];
                cbGetInfo(res);
                
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                    console.log("error")
            }
        });
    }

    function cbGetInfo(res){
        var ff = document.f1;
        var o = res;

        ff.qna_no.value = o.qna_no;
        
        ff.tpl_kind_view.value = o.kind;
        ff.qna_type.value = o.tplkind;
        ff.subject.value = o.subject;
        ff.content.value = o.content;
        
        ff.c.value = "edit";
        ff.cs_id.value = o.admin_id;
        ff.cs_nm.value = o.admin_nm;
        $("#admin").html(o.admin_nm + "[" + o.admin_id + "]");
        $("#regi_date").html(o.rt);
        $("#upd_date").html(o.ut);

        if(o.use_yn == "Y") ff.use_yn[0].checked = true;
        if(o.use_yn == "N") ff.use_yn[1].checked = true;
        // console.log(o.use_yn == "N");
        
    }

    function EnableAdd(flag){
        if(flag){
            $("[name=cs_id]").value = $("[pname=c_id]").val();
            $("[name=cs_nm]").value = $("[name=c_name]").val();
            $("#admin").html($("[name=c_name]").val() + "(" + $("[name=c_id]").val() + ")");		//로그인 정보로 등록자 세팅.

        }else{
        }
    }

    function ResetForm(){
        var f1 = document.f1;
        f1.reset();
        
        f1.use_yn[0].checked = true;
        f1.c.value = "add";
        f1.qna_no.value = '';
        
        document.getElementById("admin").innerHTML = "";
        document.getElementById("regi_date").innerHTML = "";
        document.getElementById("upd_date").innerHTML = "";	
    }

    function Validate(cmd){

        var ff = document.f1;

        if (ff.tpl_kind_view.value == '') {
            alert('템플릿 구분을 선택해 주십시오.');
            ff.tpl_kind_view.focus();
            return false;
        }

        if (ff.qna_type.value == '') {
            alert('템플릿 유형을 선택해 주십시오.');
            ff.qna_type.focus();
            return false;
        }

        if (ff.subject.value == '') {
            alert('제목을 입력해 주십시오.');
            ff.subject.focus();
            return false;
        }

        if (ff.content.value == '') {
            alert('내용을 입력해 주십시오.');
            ff.content.focus();
            return false;
        }
        return true;
    }

</script>
@stop
