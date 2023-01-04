@extends('store_with.layouts.layout-nav')
@php
    $title = "창고등록";
    if($cmd == "update") $title = "창고관리 - " . @$storage->storage_nm;
@endphp
@section('title', $title)

@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 코드관리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Cmder('{{ $cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
            {{-- @if($cmd == "update")
            <a href="javascript:void(0)" onclick="Cmder('delete')" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i>삭제</a>
            @endif --}}
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
        </div>
    </div>

    <style> 
        .required:after {content:" *"; color: red;}
        .table th {min-width:120px;}

        @media (max-width: 740px) {
            .table td {float: unset !important;width:100% !important;}
        }
    </style>

	<form name="f1" id="f1">
		<input type="hidden" name="cmd" id="cmd" value="{{ $cmd }}">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">기본 정보</a>
				</div>
				<div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">창고코드</th>
                                            <td colspan="3">
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <input type="text" name="storage_cd" id="storage_cd" value="{{ @$storage->storage_cd }}" onkeydown="setDupCheckValue()" class="form-control form-control-sm w-50 mr-2" style="max-width:280px;" @if($cmd == "update") readonly @endif />
                                                        @if($cmd == "add") 
                                                        <button type="button" class="btn btn-primary mr-2" onclick="duplicateCheckStorageCode()">중복체크</button>
                                                        @endif
                                                        <div class="custom-control custom-checkbox form-check-box mr-2">
                                                            <input type="checkbox" id="default_yn" name="default_yn" value="Y" class="custom-control-input" @if($cmd == 'update' && @$storage->default_yn == 'Y') checked @endif />
                                                            <label class="custom-control-label fs-14" for="default_yn">대표창고</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox form-check-box">
                                                            <input type="checkbox" id="online_yn" name="online_yn" value="Y" class="custom-control-input" @if($cmd == 'update' && @$storage->online_yn == 'Y') checked @endif />
                                                            <label class="custom-control-label fs-14" for="online_yn">온라인창고</label>
                                                        </div>
                                                    </div>
                                                    <p id="dupcheck" class="pt-1"></p>
                                                    <input type="hidden" name="storage_only" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">창고명칭</th>
                                            <td>
                                                <div class="form-inline">
                                                    <input type="text" name="storage_nm" id="storage_nm" value="{{ @$storage->storage_nm }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td>
                                            <th class="required">창고명칭(약칭)</th>
                                            <td>
                                                <div class="form-inline">
                                                    <input type="text" name="storage_nm_s" id="storage_nm_s" value="{{ @$storage->storage_nm_s }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주소</th>
                                            <td colspan="3">
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex mb-2">
                                                        <input type="text" name="zipcode" id="zipcode" value="{{ @$storage->zipcode }}" class="form-control form-control-sm w-50 mr-2" style="max-width:280px;" readonly />
                                                        <button type="button" class="btn btn-outline-primary" onclick="openPopupOfZipcode()"><i class="fas fa-search fa-sm mr-1"></i>검색</button>
                                                    </div>
                                                    <input type="text" name="addr1" id="addr1" value="{{ @$storage->addr1 }}" class="form-control form-control-sm mb-2 w-100" readonly />
                                                    <input type="text" name="addr2" id="addr2" value="{{ @$storage->addr2 }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>전화번호</th>
                                            <td>
                                                <div class="form-inline">
                                                    <input type="text" name="phone" id="phone" value="{{ @$storage->phone }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td>
                                            <th>FAX번호</th>
                                            <td>
                                                <div class="form-inline">
                                                    <input type="text" name="fax" id="fax" value="{{ @$storage->fax }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>대표자명</th>
                                            <td>
                                                <div class="form-inline">
                                                    <input type="text" name="ceo" id="ceo" value="{{ @$storage->ceo }}" class="form-control form-control-sm w-100" />
                                                </div>
                                            </td>
                                            <th>창고사용여부</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="use_yn_Y" name="use_yn" value="Y" @if(@$storage->use_yn != 'N') checked @endif />
                                                        <label class="custom-control-label" for="use_yn_Y">Y</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="use_yn_N" name="use_yn" value="N" @if(@$storage->use_yn == 'N') checked @endif />
                                                        <label class="custom-control-label" for="use_yn_N">N</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>LOSS창고여부</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="loss_yn_Y" name="loss_yn" value="Y" @if(@$storage->loss_yn != 'N') checked @endif />
                                                        <label class="custom-control-label" for="loss_yn_Y">Y</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="loss_yn_N" name="loss_yn" value="N" @if(@$storage->loss_yn == 'N') checked @endif />
                                                        <label class="custom-control-label" for="loss_yn_N">N</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>매장재고조회여부</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="stock_check_yn_Y" name="stock_check_yn" value="Y" @if(@$storage->stock_check_yn != 'N') checked @endif />
                                                        <label class="custom-control-label" for="stock_check_yn_Y">Y</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="stock_check_yn_N" name="stock_check_yn" value="N" @if(@$storage->stock_check_yn == 'N') checked @endif />
                                                        <label class="custom-control-label" for="stock_check_yn_N">N</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>설명</th>
                                            <td colspan="3">
                                                <div class="form-inline">
                                                    <input type="text" name="comment" id="comment" value="{{ @$storage->comment }}" class="form-control form-control-sm w-100" />
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
</div>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" charset="utf-8">

    const is_exit_default_storage = "{{ @$is_exit_default_storage }}";
    const is_exit_online_storage = "{{ @$is_exit_online_storage }}";

    function Cmder(type) {
        if(type === "add") addStorage();
        else if(type === "update") updateStorage();
        else if(type === "delete") deleteStorage();
    }

    // 창고정보 등록
    async function addStorage() {
        if(!validation('add')) return;
        if(!window.confirm("창고정보를 등록하시겠습니까?")) return;

        if(f1.default_yn.checked && '{{ @$storage->default_yn }}' !== "Y" && is_exit_default_storage === 'true') {
            if(!confirm("해당 창고를 대표창고로 설정하실 경우, 기존에 대표창고로 설정된 창고는 대표창고에서 제외됩니다.")) return;
        }
        if(f1.online_yn.checked && '{{ @$storage->online_yn }}' !== "Y" && is_exit_online_storage === 'true') {
            if(!confirm("해당 창고를 온라인창고로 설정하실 경우, 기존에 온라인창고로 설정된 창고는 온라인창고에서 제외됩니다.")) return;
        }

        axios({
            url: `/store/standard/std03/add`,
            method: 'post',
            data: getFormInputData(),
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                location.href = "/store/standard/std03/show/" + res.data.data.storage_cd;;
            } else {
                console.log(res.data);
                alert("등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 창고정보 수정
    async function updateStorage() {
        if(!validation('update')) return;

        if('{{ @$storage->default_yn }}' === "Y" && !f1.default_yn.checked) {
            return alert("대표창고를 해제할 수 없습니다.\n타 창고를 대표창고로 수정하면 자동으로 업데이트됩니다.");
        }
        if('{{ @$storage->online_yn }}' === "Y" && !f1.online_yn.checked) {
            return alert("온라인창고를 해제할 수 없습니다.\n타 창고를 온라인창고로 수정하면 자동으로 업데이트됩니다.");
        }
        
        if(f1.default_yn.checked && '{{ @$storage->default_yn }}' !== "Y" && is_exit_default_storage === 'true') {
            if(!confirm("해당 창고를 대표창고로 설정하실 경우, 기존에 대표창고로 설정된 창고는 대표창고에서 제외됩니다.")) return;
        }
        if(f1.online_yn.checked && '{{ @$storage->online_yn }}' !== "Y" && is_exit_online_storage === 'true') {
            if(!confirm("해당 창고를 온라인창고로 설정하실 경우, 기존에 온라인창고로 설정된 창고는 온라인창고에서 제외됩니다.")) return;
        }

        if(!window.confirm("창고정보를 수정하시겠습니까?")) return;

        axios({
            url: `/store/standard/std03/update`,
            method: 'put',
            data: getFormInputData(),
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 창고정보 삭제
    async function deleteStorage() {
        if(!window.confirm("해당 창고정보를 삭제하시겠습니까?")) return;

        axios({
            url: `/store/standard/std03/delete/` + f1.storage_cd.value,
            method: 'delete',
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert("삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 폼 입력 데이터 반환
    function getFormInputData() {
        return {
            storage_cd: f1.storage_cd.value,
            storage_nm: f1.storage_nm.value,
            storage_nm_s: f1.storage_nm_s.value,
            zipcode: f1.zipcode.value,
            addr1: f1.addr1.value,
            addr2: f1.addr2.value,
            phone: f1.phone.value,
            fax: f1.fax.value,
            ceo: f1.ceo.value,
            use_yn: f1.use_yn.value,
            loss_yn: f1.loss_yn.value,
            stock_check_yn: f1.stock_check_yn.value,
            default_yn: f1.default_yn.checked ? 'Y' : 'N',
            online_yn: f1.online_yn.checked ? 'Y' : 'N',
            comment: f1.comment.value,
        }
    }

    // 창고코드 중복체크
    async function duplicateCheckStorageCode() {
        const storage_cd = $("[name=storage_cd]").val().trim();
        if(storage_cd === '') return alert("창고코드를 입력해주세요.");

        const response = await axios({ 
            url: `/store/standard/std03/dupcheck/${storage_cd}`, 
            method: 'get' 
        });
        const {data: {code, msg}} = response;

        $("#dupcheck").text("* " + msg);
        $("#dupcheck").css("color", code === 200 ? "#00BB00" : "#ff0000");
        $("[name=storage_only]").val(code === 200 ? "true" : "false");
    }

    // 창고코드값 변경 시 중복체크 false 변경
    function setDupCheckValue() {
        $("[name=storage_only]").val("false");
    }

    // 우편번호 검색하기
    function openPopupOfZipcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                f1.zipcode.value = data.zonecode;
                f1.addr1.value = data.address;
            }
        }).open();
    }

    // 저장 시 입력값 확인
    const validation = (cmd) => {
        if(cmd === "add"){
            // 창고코드 입력여부
            if(f1.storage_cd.value.trim() === '') {
                f1.storage_cd.focus();
                return alert("창고코드를 입력해주세요.");
            }
            
            // 중복체크여부 검사
            if($("[name='storage_only']").val() !== "true") return alert("창고코드를 중복체크해주세요.");
        }

        // 창고명칭 입력여부
        if(f1.storage_nm.value.trim() === '') {
            f1.storage_nm.focus();
            return alert("창고명칭을 입력해주세요.");
        }

        // 창고명칭(약칭) 입력여부
        if(f1.storage_nm_s.value.trim() === '') {
            f1.storage_nm_s.focus();
            return alert("창고명칭(약칭)을 입력해주세요.");
        }

        // 주소 입력여부
        // if(f1.zipcode.value === '') return alert("주소를 입력해주세요.");

        return true;
    }
</script>
@stop
