@extends('head_with.layouts.layout-nav')
@section('title','검색 바로가기')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">검색 바로가기</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 검색 바로가기</span>
            </div>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-primary save-btn">저장</a>
        </div>
    </div>
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">검색 정보</a>
                </div>
                <div class="card-body">
                <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>검색어</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="kwd" id="kwd" class="form-control form-control-sm" value="{{$kwd}}" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>URL</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="url" id="url" class="form-control form-control-sm" value="{{$url}}" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>검색창 출력</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="disp_yn" id="disp_y" class="custom-control-input" value="Y" @if($disp_yn!='N') checked @endif>
                                                            <label class="custom-control-label" for="disp_y">예</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="disp_yn" id="disp_n" class="custom-control-input" value="N" @if($disp_yn=='N') checked @endif>
                                                            <label class="custom-control-label" for="disp_n">아니요</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if($use_yn!='N') checked @endif />
                                                            <label class="custom-control-label" for="use_y">예</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if($use_yn=='N') checked @endif />
                                                            <label class="custom-control-label" for="use_n">아니요</label>
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
        </div>
    </form>
</div>
<script>
    const idx = '{{$idx}}';
    const ajaxType = idx ? 'put' : 'post';

    const validate = () => {
        if (!$('#kwd').val()) {
            alert("검색어를 입력해주세요.");
            $('#kwd').focus();
            return false;
        }
        
        if (!$('#url').val()) {
            alert("URL를 입력해주세요.");
            $('#url').focus();
            return false;
        }
        
        if (!$('[name=disp_yn]:checked').length === 0) {
            alert("검색창 출력을 체크해주세요.");
            return false;
        }

        if (!$('[name=use_yn]:checked').length === 0) {
            alert("사용여부를 체크해주세요.");
            return false;
        }

        return confirm("저장하시겠습니까?");
    }

    $('.save-btn').click((e) => {
        e.preventDefault();

        if (validate() === false) return;

        const data = $('form[name="detail"]').serialize();
    
        $.ajax({
            async: true,
            type: ajaxType,
            url: `/head/promotion/prm32/save/${idx}`,
            data: data,
            success: function (data) {
                alert("저장되었습니다.");
                opener?.Search?.();
                location.href=`/head/promotion/prm32/show/${data.idx}`;
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });
</script>
@stop
