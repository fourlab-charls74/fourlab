@extends('head_with.layouts.layout-nav')
@section('title','메뉴관리 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">메뉴관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>메뉴관리</span>
            </div>
        </div>
    </div>
    <!-- FAQ 세부 정보 -->
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">기본정보</a>
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
                                                <th>유형</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="kind" id="kind_m" class="custom-control-input" value="M" @if(@$menu->kind == 'M') checked @endif />
                                                            <label class="custom-control-label" for="kind_m">메뉴</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="kind" id="kind_p" class="custom-control-input" value="P" @if(@$menu->kind == 'P') checked @endif />
                                                            <label class="custom-control-label" for="kind_p">프로그램</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="kind" id="kind_s" class="custom-control-input" value="S" @if(@$menu->kind == 'S') checked @endif />
                                                            <label class="custom-control-label" for="kind_s">게시판</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="kind" id="kind_l" class="custom-control-input" value="L" @if(@$menu->kind == 'L') checked @endif />
                                                            <label class="custom-control-label" for="kind_l">링크</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="kind" id="kind_d" class="custom-control-input" value="D" @if(@$menu->kind == 'D') checked @endif />
                                                            <label class="custom-control-label" for="kind_d">구분선</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>PID</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='pid' id="pid" value='{{@$menu->pid}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>상위메뉴</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="entry" id="entry" class="form-control form-control-sm">
                                                            <option value="entry_menu">상위메뉴</option>
                                                            @foreach($pmenus as $pmenu)
                                                                <option value="{{$pmenu["menu_no"]}}" @if($pmenu["menu_no"] === @$menu->entry) selected @endif>
                                                                    @if($pmenu["lev"] === 2)&nbsp;&nbsp;&nbsp;&nbsp;> @endif{{$pmenu["kor_nm"]}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>메뉴명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='kor_nm' id="kor_nm" value='{{@$menu->kor_nm}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>영문명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='eng_nm' id="eng_nm" value='{{@$menu->eng_nm}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>URL</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='action' id="action" value='{{@$menu->action}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- <tr>
                                                <th>레벨</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='lev' id="lev" value='{{@$menu->lev}}'>
                                                    </div>
                                                </td>
                                            </tr> -->
                                            <tr>
                                                <th>상태</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="state" id="state" class="form-control form-control-sm">
                                                            <option value="">선택하세요</option>
                                                            <option value="0" @if(@$menu->state == "0") selected @endif>사용중</option>
                                                            <option value="2" @if(@$menu->state == "2") selected @endif>개발중</option>
                                                            <option value="4" @if(@$menu->state == "4") selected @endif>테스트중</option>
                                                            <option value="-1" @if(@$menu->state == "-1") selected @endif>미사용</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            <tr>
                                                <th>시스템메뉴</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="sys_menu" id="sys_menu_n" class="custom-control-input" value="N" @if(@$menu->sys_menu == 'N') checked @endif />
                                                            <label class="custom-control-label" for="sys_menu_n">시스템 메뉴 아님</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="sys_menu" id="sys_menu_u" class="custom-control-input" value="U" @if(@$menu->sys_menu == 'U') checked @endif />
                                                            <label class="custom-control-label" for="sys_menu_u">본사 시스템 관리</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="sys_menu" id="sys_menu_s" class="custom-control-input" value="S" @if(@$menu->sys_menu == 'S') checked @endif />
                                                            <label class="custom-control-label" for="sys_menu_s">파트너 시스템 관리</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
	                                            <th>키워드</th>
	                                            <td class="pb-1">
		                                            <div class="d-flex flex-column">
			                                            <div class="flax_box">
				                                            <input type='text' class="form-control form-control-sm search-enter w-100" name='keyword' id="keyword" value='{{@$menu->keyword}}'>
			                                            </div>
			                                            <p class="text-secondary fs-12 mt-1">* 쉼표( &#44; )로 구분해서 작성해주세요. ex) 상품&#44;주문&#44;회원</p>
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
    </form>

	<div class="card shadow">
		<div class="card-header mb-0">
			<div class="filter_wrap">
				<div class="fl_box">
					<a href="#">개인화 컬럼 초기화</a>
					&nbsp;
					<button onclick="init_indiv_columns('{{@$menu->pid}}'); return false;" class="btn btn-sm btn-primary">초기화</button>
				</div>
			</div>
		</div>
	</div>
	
    <div class="card shadow">
        <div class="card-header mb-0">
            <a href="#">그룹별메뉴권한</a>
        </div>
        <div class="card-body pt-2">
            <div class="card-title">
                <div class="filter_wrap">
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:250px;width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <script>
        const columns = [{
                field: "group_no",
                headerName: "그룹번호"
            },
            {
                field: "group_nm",
                headerName: "그룹",
                width: 300
            },
            {
                field: "role",
                headerName: "권한",
                cellRenderer: params => {
                    var input = document.createElement('input');
                    input.type = "checkbox";
                    input.checked = params.value;
                    input.addEventListener('click', function(event) {
                        params.value = !params.value;
                        params.node.data.role = (params.value) ? 1 : 0;
                        params.node.data.editable = 'Y';
                    });
                    return input;
                },
                cellClass: 'hd-grid-code'
            },
            {
                field: "editable",
                hide: true
            },
        ];
    </script>
</div>
<div class="resul_btn_wrap mt-3 d-block">
    <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
    @if ($code !== '')
    <a href="javascript:Delete();;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
    @endif
    <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
</div>

<script>
    let code = '{{ $code  }}';

    /**
     * @return {boolean}
     */
    function Save() {


        if ($('#kor_nm').val() === '') {
            $('#kor_nm').focus();
            alert('메뉴명을 입력해 주세요.');
            return false;
        }

        if ($('#eng_nm').val() === '') {
            $('#eng_nm').focus();
            alert('영문명을 입력해 주세요.');
            return false;
        }

        if (!confirm('저장하시겠습니까?')) {
            return false;
        }

        var frm = $('form[name="detail"]');

        //console.log(frm.serialize());

        if (code == "") {
            $.ajax({
                method: 'post',
                url: '/head/system/sys02',
                data: frm.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.code == '200') {
                        alert("정상적으로 저장 되었습니다.");
                        self.close();
                        opener.Search(1);
                    } else if (res.code == '501') {
                        alert('이미 등록 된 아이디입니다.');
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
            var roles = {};
            gx.gridOptions.api.forEachNode(function(node) {
                if (node.data.editable === 'Y') {
                    roles[node.data.group_no] = node.data.role;
                }
            });
            console.log(JSON.stringify(roles));

            $.ajax({
                method: 'put',
                url: '/head/system/sys02/' + code,
                data: frm.serialize() + '&roles=' + JSON.stringify(roles),
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res.code == '200') {
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
        if (confirm('삭제 하시겠습니까?')) {
            $.ajax({
                method: 'delete',
                url: '/head/system/sys02/' + code,
                dataType: 'json',
                success: function(res) {
                    // console.log(response);
                    if (res.code == '200') {
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
	
	function init_indiv_columns(pid) {
		if (confirm('정말 초기화 하시겠습니까? 개인화 정보는 모두 삭제됩니다.')) {
			
			let data = {
				'pid': pid,
				'type': 'E'
			}

			$.ajax({
				method: 'delete',
				url: '/head/indiv-columns/init',
				data: data,
				success: function (data) {
					console.log(data);
				},
				error: function (request, status, error) {
					console.log(request);
				}
			});
		}
	}
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = '';
        gx.Request('/head/system/sys02/' + code + '/search', data);
    }
    
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 83) {
            Save();
            e.preventDefault();
        }
    });
</script>
@stop
