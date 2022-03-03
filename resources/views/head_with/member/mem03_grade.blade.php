@extends('head_with.layouts.layout-nav')
@section('title','회원등급 변경')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">회원등급 변경</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 회원등급 변경</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="window.close();" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>

    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="#" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="SearchFormReset()">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">등급</label>
                                <div class="flax_box">
                                    <select name='group' id="group" class="form-control form-control-sm">
                                        <option value=''>선택</option>
                                        @foreach($groups as $group)
                                            <option value="{{$group->id}}">{{$group->val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                        
                        <form name="search">
                            <input type="hidden" name="in_group_nos" id="in_group_nos">
                            <input type="hidden" name="ex_group_nos" id="ex_group_nos">
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="">구입금액</label>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <div class="form-group">
                                                <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_amt_from' id='cond_amt_from' value='' onkeyup="currency(this)">
                                            </div>
                                        </div>
                                        <span class="text_line">~</span>
                                        <div class="form-inline-inner input_box">
                                            <div class="form-group">
                                                <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_amt_to' id='cond_amt_to' value='' onkeyup="currency(this)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="">구매수</label>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <div class="form-group">
                                                <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_cnt_from' id='cond_cnt_from' value='' onkeyup="currency(this)">
                                            </div>
                                        </div>
                                        <span class="text_line">~</span>
                                        <div class="form-inline-inner input_box">
                                            <div class="form-group">
                                                <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_cnt_to' id='cond_cnt_to' value='' onkeyup="currency(this)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="dlv_kind">대상등급</label>
                                <div class="flax_box">
                                    <div class="flax_box" style="width:100%;">
                                        <select id="target" class="form-control form-control-sm">
                                            <option value=''>선택</option>
                                            <option value='0'>회원</option>
                                            @foreach($groups as $group)
                                                <option value="{{$group->id}}">{{$group->val}}</option>
                                            @endforeach
                                        </select>
                                        <div class="no-gutters row my-2" style="width:100%;">
                                            <div class="col-6">
                                                <a href="javascript:;" class="btn btn-sm btn-outline-primary shadow-sm add-target-btn fs-12" style="width:calc(100% - 3px);margin-right:3px;">추가</a>
                                            </div>
                                            <div class="col-6">
                                                <a href="javascript:;" class="btn btn-sm btn-outline-primary shadow-sm del-target-btn fs-12" style="width:calc(100% - 3px);margin-left:3px;">삭제</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flax_box" style="width:100%;">
                                        <select name="target_groups" id="target_groups" size="5" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">제외 그룹</label>
                                <div class="flax_box">
                                    <div class="flax_box" style="width:100%;">
                                        <select name='ext_group' id="ext_group" class="form-control form-control-sm">
                                            <option value=''>선택</option>
                                            @foreach($groups as $group)
                                                <option value="{{$group->id}}">{{$group->val}}</option>
                                            @endforeach
                                        </select>
                                        <div class="no-gutters row my-2" style="width:100%;">
                                            <div class="col-6">
                                                <a href="javascript:;" class="btn btn-sm btn-outline-primary shadow-sm add-ext-btn fs-12" style="width:calc(100% - 3px);margin-right:3px;">추가</a>
                                            </div>
                                            <div class="col-6">
                                                <a href="javascript:;" class="btn btn-sm btn-outline-primary shadow-sm del-ext-btn fs-12" style="width:calc(100% - 3px);margin-left:3px;">삭제</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flax_box" style="width:100%;">
                                        <select name="ext_groups" id="ext_groups" size="5" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box form-inline">
                        <a href="#" class="btn-sm btn btn-primary mr-1 change-btn">등급변경</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>
<script>
    const columns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:50,
            pinned:'left'
        },
        {field:"user_id" , headerName:"아이디", pinned:'left', type:"HeadUserType"  },
        {field:"name" , headerName:"이름"},
        {field:"group_nm" , headerName:"그룹"},
        {field:"regdate", headerName:"가입일", width:100},
        {field:"ord_date" , headerName:"최근주문일"},
        {field:"ord_cnt" , headerName:"구매수", type: 'currencyType'},
        {field:"ord_amt" , headerName:"구입금액", type: 'currencyType'},
        {field:"rt" , headerName:"등록일시"},

    ];

    const pApp = new App('', {gridId: "#div-gd"});
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    pApp.ResizeGrid();

    function Search() 
	{

		if( !$('#group').val() )
		{
			alert("등급을 선택해주세요.");
			return;
		}

		if( $("#target_groups option").length == 0 )
		{
			alert("대상등급을 추가해주세요.");
			return;
		}

        const in_group = [];
        const ex_group = [];

        $('#target_groups option').each(function(){
            in_group.push(this.value);
        });

        $('#ext_groups option').each(function(){
            ex_group.push(this.value);
        });

        $('#in_group_nos').val(in_group.join(','));
        $('#ex_group_nos').val(ex_group.join(','));

        let data = $('form[name="search"]').serialize();
        gx.Request(`/head/member/mem03/search/grade`, data);
    }

    $('.add-target-btn').click(function(){
        if (!$('#target').val()) {
            alert("그룹을 선택해주세요.");
            return;
        }

        const addTarget = $('#target option:selected');

        $('#target_groups').append(addTarget);

    });

    $('.del-target-btn').click(function(){
        const delTarget = $('#target_groups option:selected');
        $('#target').append(delTarget);
    });

    $('.add-ext-btn').click(function(){
        if (!$('#ext_group').val()) {
            alert("그룹을 선택해주세요.");
            return;
        }

        const addTarget = $('#ext_group option:selected');

        $('#ext_groups').append(addTarget);
    });

    $('.del-ext-btn').click(function(){
        const delTarget = $('#ext_groups option:selected');
        $('#ext_group').append(delTarget);
    });

    $('#group').change(function(){
        if(!this.value) return;

        $.ajax({    
            type: "get",
            url: `/head/member/mem03/group-value/${this.value}`,
            success: function(data) {
                $('#cond_amt_from').val(numberFormat(data.cond_amt_from));
                $('#cond_amt_to').val(numberFormat(data.cond_amt_to));
                $('#cond_cnt_from').val(numberFormat(data.cond_cnt_from));
                $('#cond_cnt_to').val(numberFormat(data.cond_cnt_to));
            }
        });
    });

    $('.change-btn').click(function(){
        if(confirm('등급을 변경하시겠습니까?') === false) return;

        const values = [];
        const group_no = $('#group').val();
        const datas = gx.getSelectedRows();

        datas.forEach(function(data){
            values.push(data.user_id);
        });

        $.ajax({    
            type: "put",
            url: `/head/member/mem03/grade/${group_no}`,
            data : {
                'user_ids' : values.join(',')
            },
            success: function(data) {
                alert("회원등급을 변경하였습니다.");
            }
        });
    });
</script>
@stop
