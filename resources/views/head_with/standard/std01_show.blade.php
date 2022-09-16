@extends('head_with.layouts.layout-nav')
@section('title','품목관리 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">품목관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>품목관리</span>
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
                                            <col width="150px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>품목코드</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='opt_kind_cd' id="opt_kind_cd" value='{{@$opt->opt_kind_cd}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>품목명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='opt_kind_nm' id="opt_kind_nm" value='{{@$opt->opt_kind_nm}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>메모</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='memo' id="memo" value='{{@$opt->memo}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    <!-- <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='use_yn1' id="use_yn" value='{{@$opt->use_yn}}'>
                                                    </div> -->
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_yn1" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="use_yn1">Y</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_yn2" class="custom-control-input" value="N">
                                                            <label class="custom-control-label" for="use_yn2">N</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($opt_kind_cd == '')
                                                <tr style="display:none;">
                                                    <th>등록자</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <span id="admin_nm">{{ @$opt->admin_nm }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="display:none;">
                                                    <th>등록일시</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <span id="regi_date">{{ @$opt->regi_date }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="display:none;">
                                                    <th>수정일시</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <span id="upd_date">{{ @$opt->upd_date }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th>등록자</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <span id="admin_nm">{{ @$opt->admin_nm }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>등록일시</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <span id="regi_date">{{ @$opt->regi_date }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>수정일시</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <span id="upd_date">{{ @$opt->upd_date }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
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
<div class="resul_btn_wrap mt-3 d-block">
    <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
    <!-- <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a> -->
    @if ($opt_kind_cd !== '')
    <a href="javascript:Delete();;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
    @endif
    <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
</div>
<script>
    let opt_kind_cd = '{{ $opt_kind_cd  }}';
    let opt_kind_nm = '{{ $opt_kind_cd }}';


    /**
     * @return {boolean}
     */
    function Save() {
        // alert ($('#use_yn').val());

        if ($('#opt_kind_cd').val() === '') {
            $('#opt_kind_cd').focus();
            alert('품목코드를 입력해 주세요.');
            return false;
        }

        if ($('#opt_kind_nm').val() === '') {
            $('#opt_kind_nm').focus();
            alert('품목이름을 입력해 주세요.');
            return false;
        }

        if ($('[name=use_yn]:checked').length == 0) {
            $("use_yn").focus();
            alert("사용여부를 선택해 주세요.");
            return false;
        }

        if (!confirm('저장하시겠습니까?')) {
            return false;
        }

        var frm = $("form[name=detail]");

        if (opt_kind_cd == "") {
            console.log('store');
            $.ajax({
                method: 'post',
                url: '/head/standard/std01',
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
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.code=');
                        console.log(res.code);
                        console.log(res.msg);
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
        } else {
            console.log(frm.serialize());
            $.ajax({
                method: 'put',
                url: '/head/standard/std01/' + opt_kind_cd,
                data: frm.serialize(),
                dataType: 'json',
                success: function(res) {
                    console.log(res);
                    if (res.code == '200') {
                        alert("정상적으로 변경 되었습니다.");
                        self.close();
                        opener.Search(1);
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.code=');
                        console.log(res.code);
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
                url: '/head/standard/std01/' + opt_kind_cd,
                dataType: 'json',
                success: function(res) {
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

	function show(opt_kind_cd) {
		console.log('show');
		$.ajax({
			method: 'get',
			url: '/head/standard/std01/get/?opt_kind_cd=' + opt_kind_cd,
			dataType: 'json',
			success: function(res) {
				console.log('res');
				if (res.code == '200') {
					let opt = res.opt[0];
					console.log(res);
					$('#opt_kind_cd').val(opt.opt_kind_cd);
					$('#opt_kind_nm').val(opt.opt_kind_nm);
					$('#memo').val(opt.memo);
					$('#admin_nm').html(opt.admin_nm);
                    $('#regi_date').html(opt.regi_date);
					$('#upd_date').html(opt.upd_date);

					if( opt.use_yn == "Y" ){
						$('#use_yn1').attr("checked", true);
					}else{
						$('#use_yn2').attr("checked", true);
					}
					
				} else {
					console.log('요청한 값이 없습니다.')
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}
</script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        show(opt_kind_cd);
    });
</script>
@stop