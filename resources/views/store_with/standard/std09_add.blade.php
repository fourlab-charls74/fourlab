@php
if ($code == '') $title = '매장구분 추가';
else $title = '판매채널관리 수정';
@endphp

@extends('store_with.layouts.layout-nav')
@section('title', $title)
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">{{@$title}}</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/기준정보관리</span>
                    <span>/판매채널관리</span>
                    <span></span>
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
                                                <th>구분</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio" style="@if($type == 'C') display:none @endif" >
                                                            <input type="radio" name="add_type" id="add_t" class="custom-control-input" value="T" @if($type == 'T' || $code == '') checked @endif/>
                                                            <label class="custom-control-label" for="add_t">매장구분</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="type1">
                                                <th>판매채널</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="sel_channel" id="sel_channel" class="form-control form-control-sm">
                                                            <option value="">선택</option>
                                                        @foreach ($channels as $c)
                                                            @if ($code == '')
                                                                <option value="{{ $c->store_channel_cd }}">{{ $c->store_channel }}</option>
                                                            @else
                                                                <option value="{{ $c->store_channel_cd }}" @if($c->store_channel_cd == $store_kind->store_channel_cd) selected @endif>{{ $c->store_channel }}</option>
                                                            @endif
                                                        @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="type2">
                                                <th>매장구분코드</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='store_kind_cd' id="store_kind_cd" onkeydown="setDupCheckValue2()" value='{{@$store_kind->store_kind_cd}}' style="width:60%" maxlength="2">
                                                        <button type="button" class="btn btn-primary ml-2" onclick="checkCode()">중복체크</button>
                                                        &nbsp;&nbsp;&nbsp; <span id="dupcheck2" class="pt-1"></span>
                                                        <input type="hidden" name="check_dup2" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="type3">
                                                <th>매장구분명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='store_kind' id="store_kind" value='{{@$store_kind->store_kind}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    @if ($type == 'C')
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if(@$store_channel->use_yn != 'N') checked @endif />
                                                                <label class="custom-control-label" for="use_y">사용</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if(@$store_channel->use_yn == 'N') checked @endif />
                                                                <label class="custom-control-label" for="use_n">미사용</label>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if(@$store_kind->use_yn != 'N') checked @endif />
                                                                <label class="custom-control-label" for="use_y">사용</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if(@$store_kind->use_yn == 'N') checked @endif />
                                                                <label class="custom-control-label" for="use_n">미사용</label>
                                                            </div>
                                                        </div>
                                                    @endif
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
        <div class="resul_btn_wrap mt-3 d-block">
            @if ($code != '')
                <a href="javascript:Edit();" class="btn btn-sm btn-primary submit-btn">저장</a>
            @else
                <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
            @endif
            <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
        </div>
    </div>
    <script>


        function Save() {

            if ($('#sel_channel').val() === '') {
                alert('판매채널을 선택해주세요');
                return false;
            }
            
            if ($('#store_kind_cd').val() === '') {
                $('#store_kind_cd').focus();
                alert('매장구분코드를 입력해주세요');
                return false;
            }

            if ($('#store_kind').val() === '') {
                $('#store_kind').focus();
                alert('매장구분명을 입력해주세요');
                return false;
            }

            if($("[name='check_dup2']").val() !== "true") return alert("매장구분코드 중복체크를 해주세요.");

            if(!confirm('저장하시겠습니까?')){
                return false;
            }

            var frm = $('form[name=detail]').serialize();

            $.ajax({
                method: 'post',
                url: '/store/standard/std09/store-type-save',
                data: frm,
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
                        alert("정상적으로 저장 되었습니다.");
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

       

        // 판매채널코드 중복체크
        async function checkCode() {
            const store_kind_cd = $("[name=store_kind_cd").val().trim();
            const add_type = $("input[name='add_type']:checked").val();
            
            if (add_type === 'C') {
                if( store_channel_cd === '' )	return alert("판매채널코드를 입력해주세요.");
                url = `/store/standard/std09/check-code/${store_channel_cd}/${add_type}`
            } else {
                if( store_kind_cd === '' )	return alert("매장구분코드를 입력해주세요.");
                url = `/store/standard/std09/check-code/${store_kind_cd}/${add_type}`
            }

            const response = await axios({ 
                url: url, 
                method: 'get' 
            });
            const {data: {code, msg}} = response;
            if (add_type === 'C') {
                $("#dupcheck").text("* " + msg);
                $("#dupcheck").css("color", code === 200 ? "#00BB00" : "#ff0000");
                $("[name=check_dup]").val(code === 200 ? "true" : "false");
            } else {
                $("#dupcheck2").text("* " + msg);
                $("#dupcheck2").css("color", code === 200 ? "#00BB00" : "#ff0000");
                $("[name=check_dup2]").val(code === 200 ? "true" : "false");
            }
        }

        //판매채널코드 값 변경시 false처리
        function setDupCheckValue() {
            $("[name=check_dup]").val("false");
        }

        //매장구분코드 값 변경시 false처리
        function setDupCheckValue2() {
            $("[name=check_dup2]").val("false");
        }

    </script>
    
@stop
