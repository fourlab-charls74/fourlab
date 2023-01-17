@extends('store_with.layouts.layout-nav')
@section('title','그룹관리 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">그룹관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>그룹관리</span>
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
                                                <th>그룹명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='group_nm' id="group_nm" value='{{@$mgr_group->group_nm}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>영문명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='group_nm_eng' id="group_nm_eng" value='{{@$mgr_group->group_nm_eng}}'>
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
                    <a href="#">그룹사용자</a>
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
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">메뉴 권한</a>
                </div>
                <div class="card-body pt-2">
                    <div class="card-title">
                        <div class="filter_wrap">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd2" style="height:250px;width:100%;" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        </div>
        
</div>
</form>
</div>

<div class="resul_btn_wrap mt-3 d-block">
    <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
    @if ($code !== '')
    <a href="javascript:Delete();;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
    @endif
    <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
</div>
<script>
    const columns = [{
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
            field: "id",
            headerName: "아이디",
            width: 150
        },
        {
            field: "name",
            headerName: "이름",
            width: 150
        },
        {
            field: "posi",
            headerName: "직책",
            width: 200
        },
        {
            field: "part",
            headerName: "부서",
            width: 200
        },
        {
            field: "editable",
            hide: true
        },
    ];
    const columns2 = [{
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
            field: "pid",
            headerName: "프로그램ID",
            width: 150
        },
        {
            field: "kor_nm",
            headerName: "메뉴명",
            width: 150
        }
    ];
</script>

<script>
    let code = '{{ $code  }}';

    /**
     * @return {boolean}
     */
    function Save() {

        if ($('#group_nm').val() === '') {
            $('#group_nm').focus();
            alert('그룹명을 입력해 주세요.');
            return false;
        }

        if ($('#group_nm_eng').val() === '') {
            $('#group_nm_eng').focus();
            alert('영문명을 입력해 주세요.');
            return false;
        }

        if (!confirm('저장하시겠습니까?')) {
            return false;
        }

        var frm = $('form');
        //console.log(frm.serialize());

        if (code == "") {
            $.ajax({
                method: 'post',
                url: '/store/system/sys03',
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
                    roles[node.data.id] = node.data.role;
                }
            });
            console.log(JSON.stringify(roles));

            $.ajax({
                method: 'put',
                url: '/store/system/sys03/' + code,
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
                url: '/store/system/sys03/' + code,
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
    const pApp2 = new App('', {
        gridId: "#div-gd2",
    });
    let gx;
    let gx2;

    $(document).ready(function() {
        let gridDiv = document.querySelector(pApp.options.gridId);
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        gx2 = new HDGrid(gridDiv2, columns2);
        Search();
        Search2();
    });

    function Search() {
        let data = '';
        gx.Request('/store/system/sys03/' + code + '/search', data);
    }

    function Search2() {
        let data = '';
        gx2.Request('/store/system/sys03/' + code + '/menu-search', data);
    }
</script>

@stop