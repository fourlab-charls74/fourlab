@extends('head_with.layouts.layout-nav')
@section('title','상품평')
@section('content')
<div class="container-fluid show_layout py-3">
	<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
		<div>
			<h3 class="d-inline-flex">상품평</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 회원&amp;CRM</span>
				<span>/ 상품평</span>
			</div>
		</div>
		<div>
			<a href="#" id="search_sbtn" onclick="Cmder('delcmd');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">삭제</a>
			<a href="#" id="search_sbtn" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm point-btn">적립금 지급</a>
		</div>
	</div>
	<div class="card_wrap aco_card_wrap">
		<!-- 업체 기본 정보 -->
		<div class="card shadow">
			<form name="f1" id="f1">
				<input type="hidden" name="data">
				<input type="hidden" name="ac_id">
				<input type="hidden" name="no" value="{{ $no }}">
				<input type="hidden" name="goods_no" value="{{ $goods_no }}">
				<input type="hidden" name="goods_sub" value="{{ $goods_sub }}">
				<input type="hidden" name="user_id" value="{{ $user_id }}">
				<input type="hidden" name="ord_no" value="{{ $ord_no }}">
				<input type="hidden" name="ord_opt_no" value="{{ $ord_opt_no }}">
				<input type="hidden" name="cmd" id="cmd" value="">
				<div class="card-header mb-0">
					<a href="#">상품평 정보</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
										</colgroup>
										<tbody>
											<tr>
												<th>제목</th>
												<td colspan="5">
													<div class="txt_box">
														{{ $goods_title }}
													</div>
												</td>
											</tr>
											<tr>
												<th>평점/조회</th>
												<td>
													<div class="txt_box">
														@if ($goods_est == '1')
														<i class="bx bxs-star"></i>
														<i class="bx bx-star"></i>
														<i class="bx bx-star"></i>
														<i class="bx bx-star"></i>
														<i class="bx bx-star"></i>
														@elseif ($goods_est == '2')
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bx-star"></i>
														<i class="bx bx-star"></i>
														<i class="bx bx-star"></i>
														@elseif ($goods_est == '3')
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bx-star"></i>
														<i class="bx bx-star"></i>
														@elseif ($goods_est == '4')
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bx-star"></i>
														@elseif ($goods_est == '5')
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														<i class="bx bxs-star"></i>
														@endif
														| <i class="bx bx-show-alt fs-16"></i> {{ number_format($cnt) }}
													</div>
												</td>
												<th>등록자</th>
												<td>
													<div class="txt_box">
														{{ $user_group_nm }} |
														<a href="#" onclick="PopUserInfo('{{ $user_id }}');">
															{{ $name }}({{ $user_id }})
														</a>
													</div>
												</td>
												<th>등록일시</th>
												<td>
													<div class="txt_box">
														{{ $regi_date }}
													</div>
												</td>
											</tr>
											<tr>
												<th>베스트
													<!--/구분//-->
												</th>
												<td>
													<div class="flax_box">
														<div>
															<a href="#" onclick="ChangeBestYN();"><span id="best_yn">{{ $best_yn }}</span></a>
															<input type="hidden" name="best_yn" value="{{ $best_yn }}" />
														</div>
													</div>
												</td>
												<th>출력여부</th>
												<td>
													<div class="txt_box">
														<a href="#" onclick="ChangeUseYN();"><span id="use_yn">{{ $use_yn }}</span></a>
														<input type="hidden" name="use_yn" value="{{ $use_yn }}" />
													</div>
												</td>
												<th>구매여부</th>
												<td>
													<div class="txt_box">
														@if ($buy_yn == 'Y')
														@if ($ord_no != '')
														(<a href="#" onclick="PopOrder('{{ $ord_no }}', '{{ $ord_opt_no }}'); return false;">{{ $ord_no }}</a>)
														@else
														-
														@endif
														@else
														-
														@endif
													</div>
												</td>
											</tr>
											<tr>
												<th>내용</th>
												<td colspan="5">
													<div class="txt_box">
														{!! $goods_text !!}
													</div>
												</td>
											</tr>
											<tr>
												<th>상품정보</th>
												<td colspan="5">
													<div class="input_box flax_box">
														<div id="goods_info_area">
															<span id="view_img" style="display:inline-block; width:100px;vertical-align:top;">
																<a href="#" onclick="GoodsPopup('https://' + '{{ $domain }}' + '/app/product/detail/' + $('[name=goods_no]').val() + '/' + $('[name=goods_sub]').val() ); return false;">
																	<img src="{{config('shop.image_svr')}}/{{ $goods_img }}" alt="{{ $goods_nm }}" width="100" height="100" border=0 />
																</a>
															</span>
															<ul style="margin:0; padding:0; list-style-type:none; list-style:none; display:inline-block; ">
																<li id="goods_nm" style="font-size:20px; font-weight:400;">
																	<a href="#" onClick="openHeadProduct('{{ $goods_no }}');return false;">{{ $goods_nm }}</a>
																	<b style="color: red; font-size:13px;font-weight:500;">[{{ $sale_stat_cl }}]</b>
																</li>
																<li id="goods_info">
																	재고(<span onclick="openHeadStock('{{ $goods_no }}','');" style="cursor: pointer;"><b style="font-size: 14px; color: red;">{{ number_format($good_qty) }}</b> /
																		<b style="font-size: 14px; color: red;">{{ number_format($wqty) }}</b></span>)
																</li>
																<li>상품평 :
																	<b style="font-size: 14px;">{{ number_format($goods_info['goods_est_cnt']) }}</b> 건,
																	평균 평점: <b>{{ $goods_info['goods_est_avg'] }}</b>
																</li>
															</ul>
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
			</form>
		</div>
		<!-- 댓글 0 개  -->
		<div class="card shadow">
			<form name="f2">
				<input type="hidden" name="goods_no">
				<input type="hidden" name="goods_sub">
				<div class="card-header mb-0">
					<h5 class="m-0 font-weight-bold">댓글 {{ count($comment_list) }}개</h5>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<thead>
											<tr>
												<th>내용</th>
												<th>등록자</th>
												<th>등록일시</th>
												<th>관리</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td colspan="3">
													@if ( @count($templates) > 0 )
													후기 댓글 템플릿 :
													<select name="template" class="form-control form-control-sm" onchange="GetTemplate(this);" style="width:auto; display:inline-block;">
														@foreach ($templates as $template)
														<option value="{{ $template['nm'] }}">{{ $template['val'] }}</option>
														@endforeach
													</select>
													<a href="javascript:void();" onclick="document.f2.comment.value = '';document.f2.comment.focus();">초기화</a>
													@endif
													<textarea id="rv_cmt" name="comment" style="resize:both;width:100%; height:70px;">{{ $tpl_comment }}</textarea>
												</td>
												<td style="text-align:center;">
													<a href="#" onclick="AddComment();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">등록</a>
													<br /><br />
													<a href="#" onclick="document.f2.reset();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">취소</a>
												</td>
											</tr>
											@foreach($comment_list as $comment_item)
											<tr>
												<td>{!! nl2br($comment_item->comment) !!}</td>
												<td><a href="#" onclick="PopUserInfo('{{ $comment_item->user_id }}')"> {{ $comment_item->user_nm }}({{ $comment_item->user_id }})</a></td>
												<td>{{ $comment_item->rt }}</td>
												<td><a href='javascript:DelComment("{{ $comment_item->cmt_no }}");'>삭제</a></td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	function GetTemplate(obj) {
		var qna_no = obj.value;
		//console.log("qna_no : "+ qna_no);

		$.ajax({
			method: 'get',
			url: '/head/member/mem22/get_template/' + qna_no,
			success: function(data) {
				if (data.return_code == 1) {
					document.f2.comment.value = data.ans_msg;
				}
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	function ChangeUseYN() {
		var use_yn = $("[name=use_yn]").val();
		var no = $("[name=no]").val();

		use_yn = (use_yn == 'Y') ? "N" : "Y";

		$.ajax({
			method: 'put',
			url: '/head/member/mem22/change-use-yn/',
			data: {
				'est_no': no,
				'use_yn': use_yn
			},
			success: function(data) {
				if (data.return_code == 1) {
					document.location.reload();
				} else {
					alert("에러가 발생했습니다.다시 한번 시도하여 주십시오.");
				}
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	// 베스트 타입 변경
	function ChangeBestType() {

		var ff = document.f1;
		var no = ff.no.value;
		var best_type = ff.best_type.value;

		if (ff.best_yn.value == 'Y') {
			if (confirm('변경하시겠습니까?')) {
				var est_no = ff.no.value;

				$.ajax({
					method: 'put',
					url: '/head/member/mem22/change_best_type/',
					data: {
						'est_no': no,
						'best_type': best_type
					},
					success: function(data) {
						if (data.return_code == 1) {
							document.location.reload();
						} else if (data.return_code == "-1") {
							alert('베스트 상품평이 아닙니다. 상품평을 베스트로 변경 후 다시 변경 해 주십시오.');
						} else {
							alert("에러가 발생했습니다.다시 한번 시도하여 주십시오.");
						}
					},
					error: function(request, status, error) {
						console.log("error");
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});

			}
		} else {
			alert('베스트구분은 베스트 상품평 일때만 변경이 가능합니다.');
		}
	}


	// 회원정보 팝업
	function PopUserInfo(memId) {
		//const url='/head/member/mem01?cmd=edit&user_id='+memId;
		const url = '/head/member/mem01/show/edit/' + memId;
		const user = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}


	function GoodsPopup(url) {
		//window.open(url,"","toolbar=1,location=1,directories=1,status=1,resizable=1,menubar=1,scrollbars=1",800,600);
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}

	function PopOrder(ord_no, ord_opt_no) {
		var url = "/head/order/ord01/" + ord_no + "/" + ord_opt_no;
		//window.open(url,"","toolbar=1,location=1,directories=1,status=1,resizable=1,menubar=1,scrollbars=1",800,600);
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}

	function ChangeBestYN() {
		var best_yn = $("[name=best_yn]").val();
		var no = $("[name=no]").val();

		best_yn = (best_yn == 'Y') ? "N" : "Y";

		$.ajax({
			method: 'put',
			url: '/head/member/mem22/change-best-yn/',
			data: {
				'est_no': no,
				'best_yn': best_yn
			},
			success: function(data) {
				if (data.return_code == 1) {
					document.location.reload();
				} else {
					alert("에러가 발생했습니다.다시 한번 시도하여 주십시오.");
				}
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	function Cmder(cmd) {
		if (cmd == 'delcmd') {
			DelCmd(cmd);
		}
	}

	function DelCmd(cmd) {
		var data = document.f1.no.value;

		if (!confirm("삭제 하시겠습니까?")) {
			return false;
		}

		$.ajax({
			method: 'put',
			url: '/head/member/mem22/delcmd',
			data: {
				'data': data
			},
			success: function(data) {
				if (data.return_code == 1) {
					window.opener.Search();
					self.close();
				} else {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.\n" + data.return_code);
				}
			},
			error: function(request, status, error) {
				console.log("error");
				console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
			}
		});

	}

	function AddComment() {
		if ($("[name=comment]").val() == '') {

			alert("댓글을 입력해 주십시오.");
			$("[name=comment]").focus();
			return false;

		} else {

			// 댓글등록
			var no = document.f1.no.value;
			var f2 = $("[name=f2]");
			var comment = $("[name=comment]").val();
			var goods_no = $("[name=goods_no]").val();
			var goods_sub = $("[name=goods_sub]").val();

			$.ajax({
				method: 'post',
				url: '/head/member/mem22/add_comment',
				data: {
					'no': no,
					'comment': comment,
					'goods_no': goods_no,
					'goods_sub': goods_sub
				},
				success: function(data) {
					if (data.return_code == "1") {
						//GetComment();
						alert("등록되었습니다.");
						location.reload();
					} else {
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.\n" + data.return_code);
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	}

	function DelComment(cmt_no) {

		if (!confirm("삭제하시겠습니까?")) {
			return false;
		}

		// 댓글삭제
		var f2 = document.f2;

		$.ajax({
			method: 'put',
			url: '/head/member/mem22/del_comment/' + cmt_no,
			success: function(data) {
				if (data.return_code == "1") {
					//GetComment();
					location.reload();
				} else {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.\n" + data.return_code);
				}
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});

	}

	$(function() {
		//GetComment();

		$('.point-btn').click(function(e) {
			var user_id = $('[name=user_id]').val();
			var no = $('[name=no]').val();
			var ord_no = $('[name=ord_no]').val();
			
			const user_ids = [];
			user_ids.push(user_id + '|' + no + '|' + ord_no);

			openAddPoint(user_ids.join(','), 11, 'pparent');
		});

	});
</script>

@stop
