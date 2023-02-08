@extends('head_with.layouts.layout-nav')
@section('title','카테고리')
@section('content')
    <script type="text/javascript" src="/handle/editor/editor.js"></script>
    <script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
    <script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<div class="show_layout py-3 px-sm-3">
    <!-- FAQ 세부 정보 -->
    <form name="f1">
        <input type="hidden" name="data" />
        <input type="hidden" name="ac_id" />
        <input type="hidden" name="cmd" value="{{$cmd}}" />
        <input type="hidden" name="cat_type" value="{{$cat_type}}" />
        <input type="hidden" name="member_group" value="" />
        <input type="hidden" name="sub_cat_cnt" value="{{$sub_cat_cnt}}" />
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">카테고리</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 카테고리</span>
                </div>
            </div>
            <div>
			    <a href="javascript:;" class="btn btn-sm btn-primary shadow-sm save-btn"  onclick="Cmder(@if($cmd == 'editcmd') 'editcmd' @else 'addcmd' @endif);"><i class="bx bx-save mr-1"></i>저장</a>
                @if($cmd == "editcmd")
                <a href="javascript:;" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="far fa-trash-alt fs-12"></i> 삭제</a>
                @endif
            </div>
        </div>
        <div class="card_wrap aco_card_wrap"><!-- 업체 기본 정보 -->
            <div class="card shadow">
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
										</colgroup>
										<tbody>
											<tr>
												<th>구분</th>
												<td>
                                                    <div class="txt_box">
                                                        <input type="hidden" name="cat_type" id="cat_type" value="{{$cat_type}}">
                                                        @IF($cat_type_nm == '')
                                                            @IF($cat_type == 'DISPLAY')
                                                                전시카테고리
                                                            @ELSEIF($cat_type == 'ITEM')
                                                                용도카테고리
                                                            @ENDIF
                                                        @ELSE
                                                            {{$cat_type_nm }}
                                                        @ENDIF
                                                    </div>
												</td>
												<th>상위카테고리</th>
												<td>
                                                    <div class="txt_box">
                                                        @IF($p_full_nm == '')
                                                           &nbsp;
                                                        @ELSE
                                                            {{$p_full_nm}}
                                                        @ENDIF
                                                    </div>
												</td>
												<th>코드</th>
												<td>
                                                    <div class="txt_box">
                                                        {{ $d_cat_cd }}
                                                        <input type="hidden" name="d_cat_cd" value="{{$d_cat_cd}}" />
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>이름</th>
												<td colspan="5">
													<div class="flax_box txt_box">
														<div class="input_box">
															<input class="form-control form-control-sm search-enter" type='text' name="d_cat_nm" value="{{$d_cat_nm}}">
														</div>
														<div id="chenge_category_area" class="flax_box" @if($cmd == 'addcmd') style="display: none !important;" @endif>
															<div class="txt_box mx-1" style="line-height:28px;">→</div>
															<div class="inline_btn_box">
																<input type="text" class="form-control form-control-sm" name='chg_d_cat_nm' id='chg_d_cat_nm' value=""/>
																<a href="javascript:;" onclick="Cmder('popup_cat');" class="btn btn-sm btn-outline-primary"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
															</div>
															<div class="custom-control custom-checkbox form-check-box ml-1">
																<input type="hidden" class="input" name='chg_d_cat_cd' id='chg_d_cat_cd' value=""/>
																<input type="checkbox" id="create_low" name="create_low" class="custom-control-input" value="1">
																<label class="custom-control-label" for="create_low">(선택된 카테고리까지)</label>
															</div>
															<a href="#" onclick="Cmder('change_category');/*Cmder('change_category');*/" class="btn btn-sm btn-primary shadow-sm ml-1">변경</a>
															<a href="#" onclick="Cmder('copy_category');" class="btn btn-sm btn-primary shadow-sm ml-1">복사</a>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>정렬</th>
												<td colspan="5">
                                                    <div class="input_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="sort_opt" id="sort_opt_a" class="custom-control-input" value="A" @IF($sort_opt != "M") checked="true" @ENDIF>
                                                                <label class="custom-control-label" for="sort_opt_a">자동(A)</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="sort_opt" id="sort_opt_m" class="custom-control-input" value="M" @IF($sort_opt == "M") checked="true" @ENDIF>
                                                                <label class="custom-control-label" for="sort_opt_m">수동(M)</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="sort_out_of_stock" id="sort_out_of_stock" class="custom-control-input" value="Y" @IF($sort_out_of_stock == "Y") checked='true' @ENDIF>
                                                                <label class="custom-control-label" for="sort_out_of_stock">품절상품은 맨뒤로</label>
                                                            </div>
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>사용여부</th>
												<td colspan="5">
                                                    <div class="input_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" @IF($use_yn != "N") checked="true" @ENDIF>
                                                                <label class="custom-control-label" for="use_yn_y">사용</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N" @IF($use_yn == "N") checked="true" @ENDIF>
                                                                <label class="custom-control-label" for="use_yn_n">미사용</label>
                                                            </div>
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>상단정보</th>
												<td colspan="5">
                                                    <div class="input_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="apply_yn" id="apply_y" class="custom-control-input" value="Y">
                                                                <label class="custom-control-label" for="apply_y">하위카테고리 모두 적용</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="apply_yn" id="apply_n" class="custom-control-input" value="N" checked>
                                                                <label class="custom-control-label" for="apply_n">하위카테고리 적용 안함</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <textarea name="header_html" id="header_html" class="form-control editor1">{{$header_html}}</textarea>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>권한</th>
												<td colspan="5">
                                                    <div class="input_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="auth" id="auth_a" class="custom-control-input" value="A" @IF($auth != "G") checked @ENDIF>
                                                                <label class="custom-control-label" for="auth_a" onclick="GroupArea(false);">전체(A)</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="auth" id="auth_g" class="custom-control-input" value="G" @IF($auth == "G") checked @ENDIF>
                                                                <label class="custom-control-label" for="auth_g" onclick="GroupArea(true);">그룹(G)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="member_group_area" class="mt-2" style="height: 150px; margin-bottom:15px; @IF($auth == 'A')display: none;@ENDIF ">
                                                        <div class="table-responsive" style=" height:150px;">
                                                            <div id="div-gd" style="width:100%;" class="ag-theme-balham"></div>
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>순서</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <input type=hidden name="is_cats">
                                                        <input type=hidden name="cats" values="">
                                                        <select class="form-control form-control-sm" style="height:150px;width:50%" multiple size=10 name="seq_cats"></select>
                                                        <div>
                                                            <input onclick="up_top_cat('seq_cats');" class=button type=button value="▲">
                                                            <input onclick="up_cat('seq_cats');" class=button type=button value="△">
                                                            <input onclick="down_cat('seq_cats');" class=button type=button value="▽">
                                                            <input onclick="down_bottom_cat('seq_cats');" class=button type=button value="▼">
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>베스트상품 출력여부</th>
												<td colspan="5">
                                                    <div class="input_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="best_display_yn" id="best_display_y" class="custom-control-input" value="Y" @IF($best_display_yn == 'Y') checked="checked" @ENDIF>
                                                                <label class="custom-control-label" for="best_display_y">예</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="best_display_yn" id="best_display_n" class="custom-control-input" value="N" @IF($best_display_yn != 'Y') checked="checked" @ENDIF>
                                                                <label class="custom-control-label" for="best_display_n">아니요</label>
                                                            </div>
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>판매처</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <select class="form-control form-control-sm" name=site>
                                                            <option value="">없음</option>
                                                            @foreach ($sites as $site_arr)
                                                                <option value='{{ $site_arr->com_id }}' 
                                                                    @IF($site_arr->com_id ==$site)
                                                                    selected
                                                                    @ENDIF    
                                                                >{{ $site_arr->com_nm }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>등록자</th>
												<td>
                                                    {{$admin_nm}}({{$admin_id}})
												</td>
												<th>등록일시</th>
												<td>
                                                    {{$regi_date}}
												</td>
												<th>수정일시</th>
												<td>
                                                    {{$upd_date}}
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
        </div>
    </form>

</div>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >
<script language="javascript">
var columns = [
        // this row shows the row index, doesn't use any data from the row
        
        {
            headerName: '',pinned:'left',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28,
            cellRenderer: function(params) {
                if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
                    return "<input type='checkbox' checked/>";
                }
            }
        },
        
        {field:"group_nm",headerName:"그룹이름",width:140,cellStyle:StyleGoodsTypeNM,editable: true},
        {field:"dc_ratio",headerName:"할인율(%)", minWidth:100,},
        {field:"point_ratio",headerName:"추가적립율(%)", width:110,},
        {field:"group_no",headerName:"group_no", hide:true,
        },
        {field:"group_cd",headerName:"group_cd", hide:true},
];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        
        Search(1);
        gx.gridOptions.api.setInfiniteRowCount(130, false);
        
        
        
    });
   
   
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
        let data = $('form[name="f1"]').serialize();
        gx.Request('/head/standard/std04/GetMemGroup', data, page, checkMemGroup);
    }

    

</script>
<script type="text/javascript" charset="utf-8">
    var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 150,
            width:600,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/head/std04',
                maxWidth:700,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions);
        
    });

    function checkMemGroup(){
        gx.gridOptions.api.forEachNode(function (node) {
            if(node.data.group_cd == node.data.group_no){
                node.setSelected(node.data.group_cd == node.data.group_no);
            }
        });
    }
</script>
<script>
    const catCd = '{{$d_cat_cd}}';
	var seq_cats = "{{$seq_cats}}";

    /*
    * 명령어 함수
    */
    function Cmder(cmd){
        //return false;
        if(cmd == "popup_cat"){	//카테고리 팝업 창
            const cat_type = $("[name=cat_type]").val();
            popCategory(cat_type);
        } else if (cmd == "addcmd" || cmd == "editcmd") {	//카테고리 저장(등록, 수정)
            if(Validate(cmd)) {
                _d_cat_cd = f1.d_cat_cd.value;
                SaveCmd(cmd);
            }
        } else if(cmd == "delcmd") { // 카테고리삭제
            if(confirm('카테고리를 삭제하시겠습니까?\n카테고리 삭제 시 상품의 전시 정보도 함께 삭제됩니다.')){
                DelCmd(cmd);
            }
        } else if (cmd == "change_category"){	//카테고리 변경
            ChangeCategory(cmd);
        } else if (cmd == "copy_category"){		//카테고리 복사
            CopyCategory(cmd);
        }
        else if (cmd == "copy_all_category") // 하위 카테고리까지 모두 복사
        {
            CopyAllCategory(cmd);
        }
    }

    const popCategory = (type) => {
        if (type === "DISPLAY") {
            searchCategory.Open(type, function(code, name, full_name, mx_len) {
                if (searchCategory.type === "ITEM") return alert("해당 카테고리는 전시 카테고리만 설정가능합니다.");
                $("#chg_d_cat_cd").val(code);
                $("[name=chg_d_cat_nm]").val(full_name);
            }) ;
        } else if (type === "ITEM") {
            searchCategory.Open(type, function(code, name, full_name, mx_len) {
                if (searchCategory.type === "DISPLAY") return alert("해당 카테고리는 용도 카테고리만 설정가능합니다.");
                $("#chg_d_cat_cd").val(code);
                $("[name=chg_d_cat_nm]").val(full_name);
            });
        };
    };

    function ChangeCategory(cmd){
        if($("[name=chg_d_cat_cd]").val() == ""){
            alert("변경할 카테고리를 선택해 주십시오.");
            return false;
        }

        $("[name=cmd]").val(cmd);
        var f1 = $("form[name=f1]");

        /*
        alert("devel.netpx.co.kr/에서 기능 작동 안함");
        return false;
        */

        //catCdCopy
        $.ajax({
            async: true,
            type: 'put',
            url: `/head/standard/std04/detail/${catCd}`,
            data: f1.serialize()+"&UID=" + Math.random(),
            success: function (data) {
                //console.log(data);
                alert("카테고리 상품이 변경되었습니다.");
                opener.Search(1);
                self.close();
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
                
                console.log("error")
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                
            }
        });
    }

    function CopyCategory(cmd){
        if($("[name=chg_d_cat_cd]").val() == ""){
            alert("복사할 카테고리를 선택해 주십시오.");
            return false;
        }

        $("[name=cmd]").val(cmd);
        var f1 = $("form[name=f1]");
        /*
        alert("devel.netpx.co.kr/에서 기능 작동 안함");
        return false;
        */

        //catCdCopy
        $.ajax({
            async: true,
            type: 'put',
            url: `/head/standard/std04/detail/${catCd}`,
            data: f1.serialize()+"&UID=" + Math.random(),
            success: function (data) {
                //console.log(data);
                alert("카테고리 상품이 복사되었습니다.");
                opener.Search(1);
                self.close();
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
                /*
                console.log("error")
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                */
            }
        });
    }

    function Validate(cmd){

        var f1 = document.f1;
        var selectedRowData = gx.gridOptions.api.getSelectedRows();

        if ($("[name=d_cat_nm]").value == "") {
            alert("카테고리 명을 입력해 주십시오.");
            $("[name=d_cat_nm]").focus();
            return false;
        }

        //권한 그룹값
        var member_group = "";
        

        selectedRowData.forEach( function(selectedRowData, index) {
            if(selectedRowData.group_no != ""){
                //data.push(selectedRowData.group_no +"_"+ selectedRowData.goods_sub);
                member_group += (member_group != "") ? "," + selectedRowData.group_no : selectedRowData.group_no;
            }
        });
        f1.member_group.value = member_group;

        //접근권한
        if(f1.auth[1].checked){

            if(f1.member_group.value == ""){
                alert("그룹을 선택해 주십시오.");
                return false;
            }
        }

        if(f1.is_cats.value == "Y"){
            var cats = "";
            var obj = f1.seq_cats;
            for(i=0;i<obj.length;i++){
                if(i ==0){
                    cats = obj.options[i].value;
                } else {
                    cats += '\t'+obj.options[i].value;
                }
            }
            f1.cats.value = cats;
        }

        return true;
    }

    /*
    * 카테고리 저장
    */
    function SaveCmd(cmd){
        var f1 = $("form[name=f1]");
        var go_url = "";
        // cmd = $("[name=cmd]").val();

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/standard/std04/detail',
            data: f1.serialize()+"&UID=" + Math.random(),
            success: function (data) {
                //console.log(data);
				if(cmd == "editcmd"){
					alert("수정되었습니다.");
				}else{
					alert("등록되었습니다.");
				}
				if(data.result_code != "1"){
					alert("처리 중 오류가 발생하였습니다. 관리자에게 문의하세요.");
					return false;
				}
				opener.Search(1);
	            self.close();
                //;Search(1);
                
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                    console.log("error")
                    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
        if(cmd == "editcmd"){
            //openCodePopup('');
        }else{
        }

    }

    function cbSaveCmd(res){

        ProcessingPopupShowHide();
        if(res.responseText == 1){
            opener.Search(1);
            self.close();
        } else {
            alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
        }
    }

	/*
	 * 카테고리 삭제
	 */
	function DelCmd(cmd){
		var f1;
        
		$("[name=cmd]").val("delcmd");
		f1 = $("form[name=f1]");
		$.ajax({
			async: true,
			type: 'put',
			url: `/head/standard/std04/detail/${catCd}`,
			data: f1.serialize() +"&UID=" + Math.random(),
			success: function (res) {
				//ale1rt('삭제되었습니다.');
				opener.Search(1);
				cbDelCmd(res);
				//window.close();
				
			},
			// PLAN_KKO_BET
			error: function(request, status, error) {
				//console.log(request, status, error);
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				console.log("error")
			}
		});

	}

	function cbDelCmd(res){
		//var ret =res.responseText;
		var ret =res.result_code;

		if(ret == "1"){

			var f1 = document.f1;
			var d_cat_cd = f1.d_cat_cd.value;
			var len = d_cat_cd.length;

			if(len > 3){
				_d_cat_cd = d_cat_cd.substr(0, d_cat_cd.length-3);
			}

			//카테고리삭제 후 부모창 그리드 로드
			//opener.GridListDraw();
			// 팝업창 닫기
			self.close();

		} else if(ret == "100"){
			alert('하위 카테고리를 삭제 후 삭제하여 주십시오.');
		} else {
			alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
		}
	}


    /*
    * 권한 그룹 그리드 노출
    */
    function GroupArea(flag){
        if(flag){
            document.getElementById("member_group_area").style.display = "";
        } else{
            document.getElementById("member_group_area").style.display = "none";
        }
    }

	/*
	 * 카테고리 순서 위로 이동
	 */
	function up_cat(obj) {
		var selected = $("[name="+ obj +"] option:selected"); // Selectbox의 객체 + 선택된 option을 구함. 
		$("[name=is_cats]").val("Y");
		if(selected.length<0){
			alert('카테고리를 선택해 주십시오.');
			return false;
		}

		if(selected[0].previousElementSibling == null) return; // 선택된 옵션중 가장 첫번째 옵션이 맨 위면 return.

		$(selected).each(function(index, obj){

			$(obj).insertBefore($(obj).prev());

		});
	}

	function up_top_cat(obj) {
		var objSelect = $("[name="+ obj +"]");
        $("[name=is_cats]").val("Y");
		//var obj = objSelect.find("option:selected");
		if( objSelect.find("option:selected").index() < 0){
			alert('카테고리를 선택해 주십시오.');
			return false;
		}
		//console.log($("[name="+ obj +"] option:selected").prevAll().length);
		$("[name="+ obj +"]>option").eq(0).before(objSelect.find("option:selected"));
	}


	/*
	 * 카테고리 순서 아래로 이동
	 */
	function down_cat(obj) {
		var selected = $("[name="+ obj +"] option:selected"); // Selectbox의 객체 + 선택된 option을 구함. 
        $("[name=is_cats]").val("Y");
		if(selected.length<0){
			alert('카테고리를 선택해 주십시오.');
			return false;
		}

		if(selected.last().next().length == 0) return;              // 선택된 옵션중 가장 마지막 옵션이 맨 아래면 return.

			$(selected.get().reverse()).each(function(index, obj){   // 선택된 옵션을 reverse함.(선택된 순서를 거꾸로 함.)

			$(obj).insertAfter($(obj).next());
		});
	}

	function down_bottom_cat(obj) {
		var objSelect = $("[name="+ obj +"]");
		var opt_len = objSelect.find("option").length;
        $("[name=is_cats]").val("Y");

		if( objSelect.find("option:selected").index() < 0){
			alert('카테고리를 선택해 주십시오.');
			return false;
		}else if(objSelect.find("option:selected").index()>=(opt_len-1)){
			return false;
		}
		objSelect.find("option").eq((opt_len-1)).after(objSelect.find("option:selected"));
		//console.log(opt_len);
	}


	$(function(){
		var f1 = document.f1;
		//순서 얻기
		var arr_seq_cats = seq_cats.split("\t");
		var obj_seq_cats = f1.seq_cats;

		f1.is_cats.value = "";
		obj_seq_cats.length = 0;
		
		if($("[name=cmd]").val() == "editcmd"){
			for(i = 0; i < arr_seq_cats.length; i++){
				var cats = arr_seq_cats[i].split("|");
				var seq = i + 1;
				obj_seq_cats.length++;
				obj_seq_cats.options[obj_seq_cats.length - 1].value = cats[0];
				obj_seq_cats.options[obj_seq_cats.length - 1].text = "("+seq+") " + cats[1];
			}
		}
        
	});

    /*
    * 변경 또는 복사할 전시 카테고리 세팅
    */
    function SetRepCategory(idx, text, mx_len) {

        //미설정 카테고리 변경 못함.
        if(idx == "000"){
            alert("미설정 카테고리로는 선택할 수 없습니다.\n다른 카테고리를 선택해 주십시오.");
            return false;
        }

        $("#chg_d_cat_cd").val(idx);
        $("[name=chg_d_cat_nm]").val(text);
    }

</script>
    
@stop
