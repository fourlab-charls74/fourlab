@extends('store_with.layouts.layout')
@section('title','수선관리')
@section('content')

<style>
    .required::after {
        position: relative;
        top: 2px;
    }
</style>

<div class="page_tit">
    <h3 class="d-inline-flex">수선관리 {{ @$type === 'detail' ? "상세" : "등록" }}</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        @if (@$type === "create")
        <span>/ 고객 / 수선관리 / 등록</span>
        @elseif (@$type =="detail")
        <span>/ 고객 / 수선관리 / 상세 {{ @$idx ? '- 접수번호: ' . $idx : '' }}</span>
        @endif
    </div>
</div>

<form method="get" id="f1" name="f1">
    <div class="show_layout">
        <input type="hidden" name="idx" value="{{@$idx}}"/>
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="javascript:void(0);">입력</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="20%">
                                <col width="30%">
                                <col width="20%">
                                <col width="30%">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>
                                    <label for="receipt_date" class="required">접수일자</label>
                                </th>
                                <td>
                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="receipt_date" value="{{ @$type === 'detail' ? @$row->receipt_date : $sdate }}" id="receipt_date" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </td>
                                <th>
                                    <label for="as_type" class="required">수선구분</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <select id="as_type" name="as_type" class="form-control form-control-sm">
                                            <option value="">선택</option>
                                            <option value="C" {{ (@$type === 'detail' && @$row->as_type == 'C') ? "selected" : "" }}>고객수선</option>
                                            <option value="S" {{ (@$type === 'detail' && @$row->as_type == 'S') ? "selected" : "" }}>매장수선</option>
                                            <option value="H" {{ (@$type === 'detail' && @$row->as_type == 'H') ? "selected" : "" }}>본사수선</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="sale_date">판매일자</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="docs-datepicker form-inline-inner input_box w-100">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm docs-date" name="sale_date" value="{{ @$type === 'detail' ? @$row->sale_date : '' }}" id="sale_date" autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="docs-datepicker-container"></div>
                                        </div>
                                    </div>
                                </td>
                                <th>
                                    <label for="receipt_no">접수번호</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <input type='text' class="form-control form-control-sm" name='receipt_no' value='{{ @$type === 'detail' ? @$row->receipt_no : '' }}'>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><!-- 추후 api 작업 예상됨 -->
                                    <label for="store_no" class="required">매장번호 / 매장명</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <input type='text' class="form-control form-control-sm search-enter" name='store_no' id="store_no" value="{{ @$type === 'detail' ? @$row->store_no : '' }}">
                                        </div>
                                        <span class="text_line">/</span>
                                        <div class="form-inline-inner input_box">
                                            <input type="text" class="form-control form-control-sm search-enter" name="store_nm" id="store_nm" value="{{ @$type === 'detail' ? @$row->store_nm : '' }}">
                                        </div>
                                    </div>
                                </td>
                                <th>
                                    <label for="item" class="required">품목</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <select name="item" id="item" class="form-control form-control-sm">
                                            <option value="">전체</option>
                                            @foreach ($items as $item)
                                                @if (@$type === 'detail')
                                                <option value="{{ $item->cd }}" {{ @$item->cd == @$row->item ? "selected" : "" }} >{{ $item->val }}</option>
                                                @else
                                                <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><!-- 추후 api 작업 예상됨 -->
                                    <label for="as_place">수선처</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <input type='text' class="form-control form-control-sm" name='as_place' value="{{ @$type === 'detail' ? @$row->as_place : '' }}">
                                    </div>
                                </td>
                                <th><!-- 추후 api 작업 예상됨 -->
                                    <label for="customer_no" class="required">고객번호 / 고객명</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <input type="text" class="form-control form-control-sm search-enter" name='customer_no' id="customer_no" value="{{ @$type === 'detail' ? @$row->customer_no : '' }}">
                                        </div>
                                        <span class="text_line">/</span>
                                        <div class="form-inline-inner input_box">
                                            <input type="text" class="form-control form-control-sm search-enter" name="customer" id="customer" value="{{ @$type === 'detail' ? @$row->customer : '' }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="item" class="required">핸드폰</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <div class="form-inline mr-0 mr-sm-1" style="width:100%;">
                                            <div class="form-inline-inner input_box" style="width:30%;">
                                                <input type="text" id="mobile1" name="mobile[]" class="form-control form-control-sm" maxlength="3" value="{{ @$type === 'detail' ? @$row->mobile[0] : '' }}" onkeyup="onlynum(this)">
                                            </div>
                                            <span class="text_line">-</span>
                                            <div class="form-inline-inner input_box" style="width:29%;">
                                                <input type="text" id="mobile2" name="mobile[]" class="form-control form-control-sm" maxlength="4" value="{{ @$type === 'detail' ? @$row->mobile[1] : '' }}" onkeyup="onlynum(this)">
                                            </div>
                                            <span class="text_line">-</span>
                                            <div class="form-inline-inner input_box" style="width:29%;">
                                                <input type="text" id="mobile3" name="mobile[]" class="form-control form-control-sm" maxlength="4" value="{{ @$type === 'detail' ? @$row->mobile[2] : '' }}" onkeyup="onlynum(this)">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <th>
                                    <label for="addr2" onclick="openFindAddress('zipcode', 'addr1')">집주소</label>
                                </th>
                                <td>
                                    <div class="input_box flex_box address_box">
                                        <input type="text" id="zipcode" name="zipcode" class="form-control form-control-sm" value="{{ @$type === 'detail' ? @$row->zipcode : '' }}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                        <input type="text" id="addr1" name="addr1" class="form-control form-control-sm" value="{{ @$type === 'detail' ? @$row->addr1 : '' }}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                        <input type="text" id="addr2" name="addr2" class="form-control form-control-sm" value="{{ @$type === 'detail' ? @$row->addr2 : '' }}" style="width:calc(25% - 10px);margin-right:10px;">
                                        <a href="javascript:;" onclick="openFindAddress('zipcode', 'addr1')" class="btn btn-sm btn-primary shadow-sm fs-12" style="width:80px;">
                                            <i class="fas fa-search fa-sm text-white-50"></i>
                                            검색
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="product" class="required">상품</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <input type="text" class="form-control form-control-sm" name="product" id="product" value="{{ @$type === 'detail' ? @$row->product : '' }}" autocomplete="off">
                                    </div>
                                </td>
                                <th>
                                    <label for="color">칼라 / 사이즈</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='color' id="color" value="{{ @$type === 'detail' ? @$row->color : '' }}">
                                        </div>
                                        <span class="text_line">/</span>
                                        <div class="form-inline-inner input_box">
                                            <input type="text" class="form-control form-control-sm search-enter" name="size" value="{{ @$type === 'detail' ? @$row->size : '' }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="storing_nm">입고처</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <input type='text' class="form-control form-control-sm" name='storing_nm' value="{{ @$type === 'detail' ? @$row->storing_nm : '' }}">
                                    </div>
                                </td>
                                <th>
                                    <label for="quantity" class="required">수량</label>
                                </th>
                                <td>
                                    <div class="flex_box">
                                        <input type='text' class="form-control form-control-sm" name='quantity' id="quantity" onkeyup="onlynum(this)" value="{{ @$type === 'detail' ? @$row->quantity : '' }}">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="use_y" class="required">수선유료구분</label>
                                </th>
                                <td>
                                    <div class="form-inline form-radio-box">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_free" id="use_y" class="custom-control-input" value="Y" {{ (@$type === 'detail' && @$row->is_free == 'Y') ? "checked" : "" }}/>
                                            <label class="custom-control-label" for="use_y">유료</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            @if (@$type === "detail")
                                            <input type="radio" name="is_free" id="use_n" class="custom-control-input" value="N" {{ (@$row->is_free == 'N') ? "checked" : "" }}/>
                                            @else
                                            <input type="radio" name="is_free" id="use_n" class="custom-control-input" value="N" checked/>
                                            @endif
                                            <label class="custom-control-label" for="use_n">무료</label>
                                        </div>
                                    </div>
                                </td>
                                <th>
                                    <label for="charged_price">수선금액</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <input type='text' class="form-control form-control-sm search-enter" name='charged_price' id="charged_price" value="{{ @$type === 'detail' ? @$row->charged_price : '' }}" placeholder="유료비용" onkeyup="onlynum(this)">
                                        </div>
                                        <span class="text_line">/</span>
                                        <div class="form-inline-inner input_box">
                                            <input type="text" class="form-control form-control-sm search-enter" name="free_price" value="{{ @$type === 'detail' ? @$row->free_price : '' }}" placeholder="무료비용" onkeyup="onlynum(this)">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="as_content" class="required">수선내용</label>    
                                </th>
                                <td colspan="4">
                                    <div class="flex_box">
                                        <textarea name="content" id="as_content" class="form-control form-control-sm" style="height: 200px;"></textarea>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header mb-0">
                <a href="javascript:void(0);">본사처리</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="20%">
                                <col width="30%">
                                <col width="20%">
                                <col width="30%">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>
                                    <label for="h_receipt_date">본사접수일</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="docs-datepicker form-inline-inner input_box w-100">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm docs-date" name="h_receipt_date" value="{{ @$type === 'detail' ? @$row->h_receipt_date : '' }}" id="h_receipt_date" autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="docs-datepicker-container"></div>
                                        </div>
                                    </div>
                                </td>
                                <th>
                                    <label for="due_date">수선예정일</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="docs-datepicker form-inline-inner input_box w-100">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm docs-date" name="due_date" value="{{ @$type === 'detail' ? @$row->due_date : '' }}" id="due_date" autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="docs-datepicker-container"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="start_date">수선인도일</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="docs-datepicker form-inline-inner input_box w-100">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm docs-date" name="start_date" value="{{ @$type === 'detail' ? @$row->start_date : '' }}" id="start_date" autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="docs-datepicker-container"></div>
                                        </div>
                                    </div>
                                </td>
                                <th>
                                    <label for="end_date">수선완료일</label>
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <div class="docs-datepicker form-inline-inner input_box w-100">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm docs-date" name="end_date" value="{{ @$type === 'detail' ? @$row->end_date : '' }}" id="end_date" autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="docs-datepicker-container"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="h_explain">본사설명</label>
                                </th>
                                <td colspan="4">
                                    <div class="flex_box">
                                        <textarea name="h_explain" id="h_explain" class="form-control form-control-sm" style="height: 200px;"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align: center;">
            @if (@$type === "create")
            <a href="javascript:void(0);" onclick="return createAs();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
            @elseif (@$type =="detail")
            <a href="javascript:void(0);" onclick="return editAs();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>수정</a>
            <a href="javascript:void(0);" onclick="return removeAs();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>삭제</a>
            @endif            
            <a href="javascript:void(0);" onclick="return goList();"class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-list-ul mr-1"></i>목록으로 이동</a>
        </div>
    </div>
</form>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" charset="utf-8">

    window.addEventListener('DOMContentLoaded', () => {
        document.f1.content.value = "{{@$type === 'detail' ? @$row->content : ''}}";
        document.f1.h_explain.value = "{{@$type === 'detail' ? @$row->h_explain : ''}}";
    });
    
    // 주소 api
    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    const goList = () => location.href = "/store/standard/std11/";

    const createAs = async () => {        
        if (validate() === false) return;
        try {
            const response = await axios({ url: `/store/standard/std11/create`,
                method: 'post', data: $('form[name="f1"]').serialize()
            });
            const { code } = response?.data;
            if (code == 200) {
                alert("등록되었습니다.");
                goList();
            } else alert("등록 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요.");
        } catch (error) {
            console.log(error);
        }
    };

    const editAs = async () => {
        if (validate() === false) return;
        try {
            const response = await axios({ url: `/store/standard/std11/edit`,
                method: 'post', data: $('form[name="f1"]').serialize()
            });
            const { code, msg } = response?.data;
            if (code == 200) {
                alert("수정되었습니다.");
            } else alert("수정 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요.");
        } catch (error) {
            console.log(error);
        }
    };

    const removeAs = async () => {
        const idx = "{{@$idx}}";
        if (confirm("등록된 수선 정보를 삭제하시겠습니까?") === false) return;
        try {
            const response = await axios({ url: `/store/standard/std11/remove`,
                method: 'post', data: { 'idx': idx }
            });
            const { code, msg } = response?.data;
            if (code == 200) {
                alert("삭제되었습니다.");
                goList();
            } else alert("삭제 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요.");
        } catch (error) {
            console.log(error);
        }
    };

    const validate = () => {
        const f1 = document.f1;

        if ($('#receipt_date').val() == "") {
            alert("접수일자을 입력해 주십시오.");
            f1.receipt_date.focus();
            return false;
        }

        if ($('#as_type').val() == "") {
            alert("수선구분을 선택해 주십시오.");
            f1.as_type.focus();
            return false;
        }

        if ($('#store_no').val() == "") {
            alert("매장번호를 입력하여 주십시오.");
            f1.store_no.focus();
            return false;
        }

        if ($('#store_nm').val() == "") {
            alert("매장명를 입력하여 주십시오.");
            f1.store_nm.focus();
            return false;
        }

        if ($('#item').val() == "") {
            alert("품목구분을 선택해 주십시오.");
            f1.item.focus();
            return false;
        }

        if ($('#customer_no').val() == "") {
            alert("고객번호를 선택해 주십시오.");
            f1.customer_no.focus();
            return false;
        }

        if ($('#customer').val() == "") {
            alert("고객명를 선택해 주십시오.");
            f1.customer.focus();
            return false;
        }

        const mobile_reg = /^01(?:0|1|[6-9])$/;
        if (!mobile_reg.test($('#mobile1').val())) {
            alert("휴대전화 앞3자리를 확인해주세요.");
            $('#mobile1').focus();
            return false;
        }
        if ($('#mobile2').val() == "") {
            alert("휴대전화의 중간 번호를 입력해 주세요.");
            $('#mobile2').focus();
            return false;
        }
        if ($('#mobile3').val() == "") {
            alert("휴대전화의 나머지 번호를 입력해 주세요.");
            $('#mobile3').focus();
            return false;
        }
        
        if ($('#product').val() == "") {
            alert("상품을 선택해 주십시오.");
            f1.product.focus();
            return false;
        }

        if ($('#is_free').val() == "") {
            alert("수선유료구분을 선택해 주십시오.");
            f1.is_free.focus();
            return false;
        }
        
        if ($('#quantity').val() == "") {
            alert("수량을 입력해 주십시오.");
            f1.quantity.focus();
            return false;
        }

        if ($('#as_content').val() == "") {
            alert("수선내용을 입력해 주십시오.");
            f1.content.focus();
            return false;
        }

        return true;
    };

</script>

@stop