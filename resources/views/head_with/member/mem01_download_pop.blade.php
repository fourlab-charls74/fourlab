@extends('head_with.layouts.layout-nav')
@section('title','회원관리')
@section('content')
	
<style>
	.table tr th input {
		margin-left: 10px;
	}
</style>
<div class="container-fluid show_layout py-3">
	<div class="page_tit mb-3 pr-0 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">회원관리 다운로드</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 회원&amp;CRM</span>
                <span>/ 회원관리 다운로드</span>
            </div>
        </div>
        <div>
			<a href="javascript:void(0);" class="d-none d-sm-inline-block btn btn-sm btn-primary download-btn">다운로드</a>
        </div>
    </div>

	<div class="card shadow">
		<div class="card-header d-flex justify-content-between align-items-center">
			<a href="">필터</a>
			<p class="fs-14">검색인원 <b id="total_count" class="text-primary font-weight-bold"></b>명</p>
		</div>
		<div class="card-body brtn mt-0 pt-2">
			<form method="get" name="search">
				<div class="row_wrap">
					<div class="row">
						<div class="col-12">
							<div class="table-box mobile filter">
								<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
									<tbody>
                                        <colgroup>
                                            <col width="40px">
                                        </colgroup>
										<tr style="background-color: rgb(230, 230, 230)">
											<th class="hdx"><input type="checkbox" id="all_check" title="전체선택" /></th>
											<td class="hdx"><label class="txt_box mb-0" for="all_check">항목</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="user_id" value="user_id" checked /></th>
											<td class="bd"><label class="txt_box mb-0" for="user_id">아이디</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="name" value="name" checked /></th>
											<td class="bd"><label class="txt_box mb-0" for="name">이름</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="sex" value="sex" /></th>
											<td class="bd"><label class="txt_box mb-0" for="sex">성별</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="jumin" value="jumin" /></th>
											<td class="bd"><label class="txt_box mb-0" for="jumin">주민번호</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="phone" value="phone" /></th>
											<td class="bd"><label class="txt_box mb-0" for="phone">전화번호</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="mobile" value="mobile" checked /></th>
											<td class="bd"><label class="txt_box mb-0" for="mobile">휴대전화</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="email" value="email" checked /></th>
											<td class="bd"><label class="txt_box mb-0" for="email">이메일</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="point" value="point" /></th>
											<td class="bd"><label class="txt_box mb-0" for="point">적립금</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="regdate" value="regdate" /></th>
											<td class="bd"><label class="txt_box mb-0" for="regdate">가입일</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="lastdate" value="lastdate" /></th>
											<td class="bd"><label class="txt_box mb-0" for="lastdate">최근로그인</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="visit_cnt" value="visit_cnt" /></th>
											<td class="bd"><label class="txt_box mb-0" for="visit_cnt">로그인횟수</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="ord_date" value="ord_date" /></th>
											<td class="bd"><label class="txt_box mb-0" for="ord_date">최근주문일</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="ord_cnt" value="ord_cnt" /></th>
											<td class="bd"><label class="txt_box mb-0" for="ord_cnt">구매수</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="ord_amt" value="ord_amt" /></th>
											<td class="bd"><label class="txt_box mb-0" for="ord_amt">구매금액</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="name_chk" value="name_chk" /></th>
											<td class="bd"><label class="txt_box mb-0" for="name_chk">실명확인</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="email_chk" value="email_chk" /></th>
											<td class="bd"><label class="txt_box mb-0" for="email_chk">메일수신</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="mobile_chk" value="mobile_chk" /></th>
											<td class="bd"><label class="txt_box mb-0" for="mobile_chk">SMS수신</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="yn" value="yn" /></th>
											<td class="bd"><label class="txt_box mb-0" for="yn">승인</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="zip" value="zip" /></th>
											<td class="bd"><label class="txt_box mb-0" for="zip">우편번호</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="addr" value="addr" /></th>
											<td class="bd"><label class="txt_box mb-0" for="addr">주소</label></td>
										</tr>
										<tr>
											<th class="bd"><input type="checkbox" name="field" id="group" value="group" /></th>
											<td class="bd"><label class="txt_box mb-0" for="group">회원그룹</label></td>
										</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="text-center mt-3">
		<a href="#" class="btn btn-sm btn-primary download-btn">다운로드</a>
	</div>
</div>
<script>
let feilds = null;

$(document).ready(function (e) {
	let total = opener.getSearchTotalCount();
	$("#total_count").text(Comma(total));
})

$("#all_check").click(function(){
	$('[name=field]').prop("checked", this.checked);
});

$('.download-btn').click(function(){
	if (!opener) return;
	if (!opener.Download) return;
	feilds = [];
	$('[name=field]:checked').each(function() {
		feilds.push( $(`[for=${this.id}]`)[0].innerHTML + '|' + this.value);
	});

	opener.Download(feilds);
});
</script>
@stop
