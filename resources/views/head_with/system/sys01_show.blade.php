@extends('head_with.layouts.layout-nav')
@section('title','사용자관리 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">사용자관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>사용자관리</span>
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
                                                <th>등급</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="grade" id="grade_s" class="custom-control-input" value="S" @if(@$user->grade == 'S') checked @endif />
                                                            <label class="custom-control-label" for="grade_s">시스템관리</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="grade" id="grade_m" class="custom-control-input" value="M" @if(@$user->grade == 'M') checked @endif />
                                                            <label class="custom-control-label" for="grade_m">회사마스터</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="grade" id="grade_u" class="custom-control-input" value="U" @if(@$user->grade == 'U') checked @endif />
                                                            <label class="custom-control-label" for="grade_u">일반유저</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>아이디</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='id' id="id" value="{{@$user->id}}" autocomplete="off" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>비밀번호</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='password' class="form-control form-control-sm w-25" name='passwd' id="passwd" autocomplete="new-password" />
                                                        <div class="custom-control custom-checkbox form-check-box ml-2" style="@if($code == '') display:none; @endif">
                                                            <input type="checkbox" class="custom-control-input" value="Y" name="passwd_chg" id="passwd_chg" @if($code == '') checked @endif/>
                                                            <label class="custom-control-label" for="passwd_chg">비밀번호 변경 시 체크해 주십시오</label>
                                                        </div>
                                                    </div>
                                                    <p class="fs-12" style="color:red;">* 비밀번호는 6~12자 영문과 숫자가 조합되어야 합니다.</p>
                                                    <div class="flax_box pt-1">
                                                        <input type="text" name="pwchgperiod" class="form-control form-control-sm text-right w-25 mr-1" value="{{@$user->pwchgperiod ?? '0'}}" />
                                                        일 주기로 변경, 최근변경일 : {{@$user->pwchgdate}}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='name' id="name" value='{{@$user->name}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>IP</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="iptype" id="iptype_a" class="custom-control-input" value="A" @if(@$user->iptype == 'A') checked @endif />
                                                            <label class="custom-control-label" for="iptype_a">모두</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="iptype" id="iptype_l" class="custom-control-input" value="L" @if(@$user->iptype == 'L') checked @endif />
                                                            <label class="custom-control-label" for="iptype_l">제한</label>
                                                        </div>
                                                        (예 : 211.238.131.1 ~ 211.238.131.255)
                                                    </div>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='ipfrom' id="ipfrom" value='{{@$user->ipfrom}}'>
                                                        <span class="text_line p-1">~</span>
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='ipto' id="ipto" value='{{@$user->ipto}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>MD여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="md_yn" id="md_y" class="custom-control-input" value="Y" @if(@$user->md_yn != 'N') checked @endif />
                                                            <label class="custom-control-label" for="md_y">사용</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="md_yn" id="md_n" class="custom-control-input" value="N" @if(@$user->md_yn == 'N') checked @endif />
                                                            <label class="custom-control-label" for="md_n">미사용</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if(@$user->use_yn != 'N') checked @endif />
                                                            <label class="custom-control-label" for="use_y">사용</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if(@$user->use_yn == 'N') checked @endif />
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
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">부가정보</a>
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
                                                <th>부서</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-all" name='part' id='part' value='{{@$user->part}}'>
                                                    </div>
                                                </td>
                                                <th>직책</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='posi' id="posi" value='{{@$user->posi}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>연락처/내선</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='tel' id="tel" value='{{@$user->tel}}'>
                                                        <span class="text_line p-1">/</span>
                                                        <input type='text' class="form-control form-control-sm w-25" name='exttel' id="exttel" value='{{@$user->exttel}}'>
                                                    </div>
                                                </td>
                                                <th>메신저</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm" name='messenger' id="messenger" value='{{@$user->messenger}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이메일</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='email' id="email" value='{{@$user->email}}'>
                                                    </div>
                                                </td>
                                                <th></th>
                                                <td>
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
            <a href="#">그룹정보</a>
        </div>
        <div class="card-body pt-2">
            <div class="card-title">
                <div class="filter_wrap">
                </div>
            </div>
            <div class="table-responsive">
                @if($code == '')
                    <p>* 그룹정보는 사용자를 등록한 후, 상세페이지에서 설정할 수 있습니다.</p>
                @else
                    <div id="div-gd" style="height:250px;width:100%;" class="ag-theme-balham"></div>
                @endif
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

    function Save() {

        if ($('#id').val() === '') {
            $('#id').focus();
            alert('아이디를 입력해주세요.');
            return false;
        }

        if ($('#passwd_chg').is(':checked') && $("#passwd").val() === '') {
            $('#passwd').focus();
            alert('비밀번호를 입력해주세요.');
            return false;
        }

        if ($('#name').val() === '') {
            $('#name').focus();
            alert('이름을 입력해주세요.');
            return false;
        }

        if (!confirm('저장하시겠습니까?')) {
            return false;
        }

        if (!$('#passwd_chg').is(':checked') && $("#passwd").val() !== '') {
            if(!confirm("비밀번호 변경에 체크하지 않았습니다. 비밀번호 변경없이 저장하시겠습니까?")) return;
        }

        var frm = $('form[name="detail"]');

        if (code == "") {
            $.ajax({
                method: 'post',
                url: '/head/system/sys01',
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
                url: '/head/system/sys01/' + code,
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

    function Delete() {
        if (confirm('삭제 하시겠습니까?')) {
            $.ajax({
                method: 'delete',
                url: '/head/system/sys01/' + code,
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
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(550);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let c = code == '' ? '-' : code;
        gx.Request('/head/system/sys01/' + c + '/search', '');
    }
</script>
@stop