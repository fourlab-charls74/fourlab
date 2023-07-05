@extends('store_with.layouts.layout-nav')
@section('title','환경관리 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">환경관리 상세</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
	            <span>/ 시스템</span>
	            <span>/ 환경관리</span>
            </div>
        </div>
    </div>

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
											<th class="required">구분</th>
											<td>
												<div class="flax_box">
													<select id='type' name='type' class="form-control form-control-sm" @if($cmd === 'update') readonly @endif>
														<option value="">-- 선택 --</option>
														@foreach ($types as $key => $value)
															<option value='{{ $key }}' @if(@$conf->type === $key) selected @endif>{{ $value }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">이름</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm w-100" name='name' id="name" value='{{@$conf->name}}' @if($cmd === 'update') readonly @endif />
												</div>
											</td>
										</tr>
										<tr>
											<th>이름(일련번호)</th>
											<td>
												<div class="flax_box">
													<input type='hidden' name='prev_idx' id="prev_idx" value='{{@$conf->idx}}'>
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
											<th>내용</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm w-100" name='content' id="cont" value='{{@$conf->content}}'>
												</div>
											</td>
										</tr>
										<tr>
											<th>세부설명</th>
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
	<div class="resul_btn_wrap mt-3 d-block">
		<a href="javascript:Save();" class="btn btn-sm btn-primary"><i class="bx bx-save fs-16 mr-1"></i> 저장</a>
		@if ($cmd === 'update')
			<a href="javascript:Delete();" class="btn btn-sm btn-danger"><i class="bx bx-trash fs-16 mr-1"></i> 삭제</a>
		@endif
		<a href="javascript:void(0);" class="btn btn-sm btn-secondary" onclick="window.close()"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	const cmd = "{{ $cmd  }}";
	const type = "{{ @$conf->type  }}";
	const name = "{{ @$conf->name }}";
	const idx = "{{ @$conf->idx }}";

	// 환경관리 저장
	function Save() {
		if (!validation() || !confirm("저장하시겠습니까?")) return;

		const data = $('form[name=detail]').serialize();

		if (cmd === 'add') {
			// 환경관리정보 등록
			$.ajax({
				method: 'post',
				url: '/store/system/sys05',
				data: data,
				dataType: 'json',
				success: function(res) {
					if (res.code === 200) {
						alert("정상적으로 저장되었습니다.");
						opener.Search();
						self.close();
					} else if (res.code !== 500) {
						alert(res.msg);
					} else {
						alert("저장 중 오류가 발생했습니다.\n다시 시도해주세요.");
						console.error(res);
					}
				},
				error: function(e) {
					console.log(e.responseText);
				}
			});
		} else if (cmd === 'update') {
			// 환경관리정보 수정
			$.ajax({
				method: 'put',
				url: '/store/system/sys05',
				data: data,
				dataType: 'json',
				success: function(res) {
					if (res.code === 200) {
						alert("정상적으로 저장되었습니다.");
						opener.Search();
						self.close();
					} else if (res.code !== 500) {
						alert(res.msg);
					} else {
						alert("저장 중 오류가 발생했습니다.\n다시 시도해주세요.");
						console.error(res);
					}
				},
				error: function(e) {
					console.log(e.responseText)
				}
			});
		}
	}
	
	function validation() {
		if ($('#type').val() === '') {
			$('#type').focus();
			alert('구분을 선택해 주세요.');
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
		return true;
	}

	// 환경관리 상세내용 삭제
	function Delete() {
		if (!confirm("삭제된 정보는 다시 되돌릴 수 없습니다.\n삭제하시겠습니까?")) return;

		$.ajax({
			method: 'delete',
			url: '/store/system/sys05',
			data: { type, name, idx },
			dataType: 'json',
			success: function(res) {
				if (res.code === 200) {
					alert("정상적으로 삭제되었습니다.");
					opener.Search();
					self.close();
				} else {
					alert("삭제 중 오류가 발생했습니다.\n다시 시도해주세요.");
					console.error(res);
				}
			},
			error: function(e) {
				console.log(e.responseText);
			}
		});
	}
</script>
@stop
