@extends('shop_with.layouts.layout')
@section('title','매장 공지사항')
@section('content')

<div class="show_layout">
    <div class="page_tit">
        @if($store_notice_type === "notice")
            <h3 class="d-inline-flex">매장 공지사항</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 매장 공지사항</span>
            </div>
        @else 
            <h3 class="d-inline-flex">VMD 게시판</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ VMD 게시판</span>
            </div>
        @endif
    </div>
    <form>
        @csrf
        <input type="hidden" id= "store_notice_type" name="store_notice_type" value="{{ $store_notice_type }}">
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        <button type="button"
                            @if($store_notice_type === "notice")
                                onclick="document.location.href='/shop/community/comm01/notice';"
                            @else
                                onclick="document.location.href='/shop/community/comm01/vmd';"
                            @endif
                                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                        >목록
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>작성자</th>
                                        <td>
                                            <div class="txt_box">{{$user->name}}
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td>
                                            <div class="txt_box">{{$user->subject}}
                                                @error(' subject') <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td>
                                            <div>
                                                <input type="hidden" id="div_content1" name="content" value='{{$user->content}}' />
                                                <div id="div_content2" class="txt_box" style="min-height: 200px"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    @if($store_notice_type !== 'notice' || $store_notice_type == 'notice')
                                        @if(($user->attach_file_url != '' && $user->attach_file_url !== null) && count(explode(',', $user->attach_file_url)) >= 5)
                                        <th>파일 다운로드</th>
                                        <td>
                                            @foreach(explode(',', $user->attach_file_url) as $file_url) 
                                                    <a href="javascript:downloadFile('{{$file_url}}')">{{explode('/', $file_url)[3]}}</a>
                                                    &nbsp;&nbsp;
                                                    <a href="javascript:deleteFile('{{$no}}', '{{$file_url}}')">X</a>
                                                    <br/>
                                            @endforeach
                                        </td>
                                        @else
											@if ($store_notice_type == 'notice')
												<th>파일 다운로드</th>
												<td>
													@if($user->attach_file_url != '' && $user->attach_file_url !== null)
														@foreach(explode(',', $user->attach_file_url) as $file_url)
															<a href="javascript:downloadFile('{{$file_url}}')">{{explode('/', $file_url)[3]}}</a>
															<br>
														@endforeach
													@endif
												</td>
											@else 
												<th>파일 업로드 및 다운로드</th>
												<td>
													@if($user->attach_file_url != '' && $user->attach_file_url !== null)
														@foreach(explode(',', $user->attach_file_url) as $file_url)
															<a href="javascript:downloadFile('{{$file_url}}')">{{explode('/', $file_url)[3]}}</a>
															&nbsp;&nbsp;
															<a href="javascript:deleteFile('{{$no}}', '{{$file_url}}')">X</a>
															<br/>
														@endforeach
													@endif
													<div class="form-inline inline_btn_box">
														<input type = "file" name= "notice_add_file" id="notice_add_file" multiple>
													</div>
													<span style="color:red">※이미지(jpg, png), 엑셀(excel), ppt(pptx)만 가능합니다.</span>
												</td>
											@endif
                                        @endif
                                    @endif
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">

    $(document).ready(function() {
        document.getElementById('div_content2').innerHTML = $('#div_content1').val();
    });

	function getFileName (contentDisposition) {
		let fileName = contentDisposition
			.split(';')
			.filter(function(ele) {
				return ele.indexOf('filename') > -1
			})
			.map(function(ele) {
				return ele
					.replace(/"/g, '')
					.split('=')[1]
			});
		return fileName[0] ? fileName[0] : null
	}

	function downloadFile(path) {
		$.ajax({
			url: `/shop/community/comm01/file/download/${path.split('/').reverse()[0]}`,
			type: 'GET',
			cache: false,
			xhrFields: {
				responseType: 'blob'
			},
		})
			.done(function (data, status, jqXhr) {
				if (!data) {
					return;
				}

				try {
					let blob = new Blob([data], { type: jqXhr.getResponseHeader('content-type') });
					let fileName = getFileName(jqXhr.getResponseHeader('content-disposition'));
					fileName = decodeURI(fileName);

					//익스플로어
					if (window.navigator.msSaveOrOpenBlob) {
						window.navigator.msSaveOrOpenBlob(blob, fileName);
					} else {
						let link = document.createElement('a');
						let url = window.URL.createObjectURL(blob);
						link.href = url;
						link.target = '_self';
						link.download = fileName;
						document.body.append(link);
						link.click();
						link.remove();
						window.URL.revokeObjectURL(url);
					}
				} catch (e) {
					console.error(e);
				}
			});
	}
        
</script>
@stop
