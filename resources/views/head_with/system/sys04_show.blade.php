@extends('head_with.layouts.layout-nav')
@section('title','환경관리 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">환경관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>환경관리</span>
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
                                                <th>구분</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='type' id="type" value='{{@$conf->type}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='name' id="name" value='{{@$conf->name}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름(일련번호)</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='idx' id="idx" value='{{@$conf->idx}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>값</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='value' id="value" value='{{@$conf->value}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>모바일값</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='mvalue' id="mvalue" value='{{@$conf->mvalue}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>컨텐츠</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='content' id="cont" value='{{@$conf->content}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>상세</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-100" name='desc' id="desc" value='{{@$conf->desc}}'>
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
        </div>
    </form>
</div>
<div class="resul_btn_wrap mt-3 d-block">
    <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
    @if ($type !== '')
    <a href="javascript:Delete();;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
    @endif
    <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
</div>
<script>

    let type = '{{ $type  }}';
    let name = '{{ $name }}';


    /**
     * @return {boolean}
     */
    function Save() {

        if ($('#type').val() === '') {
            $('#type').focus();
            alert('타입를 꼭 입력해 주세요.');
            return false;
        }

        if ($('#name').val() === '') {
            $('#name').focus();
            alert('이름을 입력해 주세요.');
            return false;
        }
        if ($('#value').val() === '') {
            $('#value').focus();
            alert('값을 입력해 주세요.');
            return false;
        }

        // if ($('#idx').val() === '') {
        //     $('#idx').focus();
        //     alert('일련번호을 입력해 주세요.');
        //     return false;
        // }

        if (!confirm('저장하시겠습니까?')) {
            return false;
        }

        var frm = $('form');

        if (type == "") {
            console.log('store');
            $.ajax({
                method: 'post',
                url: '/head/system/sys04',
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
                        console.log(res.code);
                        console.log(res.msg);
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
        } else {
            $.ajax({
                method: 'put',
                url: '/head/system/sys04/' + type + '/' + name,
                data: frm.serialize(),
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
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
                url: '/head/system/sys04/' + type + '/' + name,
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
                    console.log(e.responseText);
                }
            });
        }
    }

    function show(type,name){
        console.log('show');
        $.ajax({
            method: 'get',
            url: '/head/system/sys04/' + type + '/' + name + '/get',
            dataType: 'json',
            success: function(res) {
                if (res.code == '200') {
                    let cnf = res.conf[0];
                    console.log(res);
                    $('#type').val(cnf.type);
                    $('#name').val(cnf.name);
                    $('#idx').val(cnf.idx);
                    $('#value').val(cnf.value);
                    $('#mvalue').val(cnf.mvalue);
                    $('#cont').val(cnf.content);
                    $('#desc').val(cnf.desc);
                } else {
                    console.log('요청한 값이 없습니다.')
                }
            },
            error: function(e) {
                console.log(e.responseText);
            }
        });
    }

</script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        show(type,name);
    });
</script>
@stop
