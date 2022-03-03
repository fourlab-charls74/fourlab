@extends('partner_with.layouts.layout-nav')
@section('title','배송정보 변경')
@section('content')

<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">배송정보 변경</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 주문</span>
                <span>/ 배송정보 변경</span>
            </div>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn">저장</a>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>

    <!-- 배송정보 -->
    <form name='dlv'>
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">배송정보</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered custm_tb1" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
											<col width="9%">
											<col width="24%">
											<col width="9%">
											<col width="24%">
											<col width="9%">
											<col width="24%">
                                        </colgroup>
										<tbody>
                                        <tr>
                                            <th>수령자</th>
                                            <td>
                                                <div class="txt_box"><input type="text" id="r_nm" name="r_nm" class="form-control form-control-sm" value="{{$ord->r_nm}}"></div>
                                            </td>
                                            <th>주문번호</th>
                                            <td>
                                                <div class="txt_box">{{$ord_no}}</div>
                                            </td>
                                            <th>전화번호</th>
                                            <td>
                                                <div class="txt_box"><input type="text" id="r_phone" name="r_phone" class="form-control form-control-sm" value="{{$ord->r_phone}}" ></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>휴대전화</th>
                                            <td>
                                                <div class="txt_box"><input type="text" id="r_mobile" name="r_mobile" class="form-control form-control-sm" value="{{$ord->r_mobile}}" ></div>
                                            </td>
                                            <th>수령지주소</th>
                                            <td colspan="5">
                                                <div class="input_box flax_box address_box">
                                                    <input type="text" id="r_zipcode" name="r_zipcode" class="form-control form-control-sm" value="{{$ord->r_zipcode}}" style="width:calc(15% - 10px);margin-right:10px;" readonly="readonly">
                                                    <input type="text" id="r_addr1" name="r_addr1" class="form-control form-control-sm" value="{{$ord->r_addr1}}" style="width:calc(30% - 10px);margin-right:10px;" readonly="readonly">
                                                    <input type="text" id="r_addr2" name="r_addr2" class="form-control form-control-sm" value="{{$ord->r_addr2}}" style="width:calc(25% - 10px);margin-right:10px;">
                                                    <a href="javascript:;" onclick="openFindAddress('r_zipcode', 'r_addr1')" class="btn btn-sm btn-primary shadow-sm" style="width:80px;">
                                                        <i class="fas fa-search fa-sm text-white-50"></i>
                                                        검색
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>배송메시지</th>
                                            <td colspan="5">
                                                <textarea name="dlv_msg" id="dlv_msg" class="form-control form-control-sm" >{{$ord->dlv_msg}}</textarea>
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
                    <a href="#" class="m-0 font-weight-bold">주문 상품 정보</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered th_border_none">
                                        <thead>
                                            <tr>
                                                <th>상태</th>
                                                <th>스타일넘버<br>(업체)</th>
                                                <th colspan="2">상품명/옵션</th>
                                                <th style="white-space:nowrap;">택배사</th>
                                                <th style="white-space:nowrap;">송장번호</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($dlvs) > 0)
                                                @foreach($dlvs as $dlv)
                                                    <tr class="{{$dlv['choice_class']}}">
                                                        <td style="text-align:center;white-space:nowrap;">
                                                            <input type="hidden" name="ord_opt_nos[]" value="{{$dlv['ord_opt_no']}}">
                                                            {{$dlv['ord_state_nm']}}
                                                        </td>
                                                        <td>
                                                            {{$dlv['style_no']}}<br>
                                                            ({{$dlv['com_nm']}})
                                                        </td>
                                                        <td style="width:50px;">
                                                            <img src="{{config('shop.image_svr')}}{{@$dlv['img']}}" alt="img" style="width:40px">
                                                        </td>
                                                        <td>
                                                            <a href="#">{{$dlv['goods_snm']}}</a><br>
                                                            {{$dlv['opt_val']}}
                                                            @foreach($dlv['addopts'] as $opt)
                                                                , {{$opt->addopt}}
                                                            @endforeach
                                                        </td>
                                                        <td class="text-right">
                                                            @if($dlv['ord_state'] > 20)
                                                                <select class="form-control form-control-sm" name="dlv_cd_{{$dlv['ord_opt_no']}}">
                                                                    <option value=''>택배사 선택</option>
                                                                    @foreach($dlv['dlv_cds'] as $dlv_cd)
                                                                        <option
                                                                            value="{{$dlv_cd->code_id}}"
                                                                            @if ($dlv_cd->code_id === $dlv['dlv_cd']) selected @endif
                                                                        > {{$dlv_cd->code_val}}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            @if($dlv['ord_state'] > 20)
                                                                <input
                                                                    type="text"
                                                                    class="form-control form-control-sm"
                                                                    name="dlv_no_{{$dlv['ord_opt_no']}}"
                                                                    value="{{$dlv['dlv_no']}}"/>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
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
<div class="text-center mb-3">
    <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 save-btn">저장</a>
    <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
</div>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<script>
    const ord_no = '{{$ord_no}}'

    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    $('.save-btn').click(function(e){
        e.preventDefault();

        if(confirm('배송정보를 변경하시겠습니까?')){
            const data = $('form[name="dlv"]').serialize();
            $.ajax({
                async: true,
                type: 'put',
                url: '/partner/order/ord01/dlv-info-save/' + ord_no,
                data: data,
                success: function (data) {
                    alert("저장되었습니다.");
                    if (opener && opener.dlvSubmitCallback) opener.dlvSubmitCallback();
                    window.close();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
</script>
@stop
