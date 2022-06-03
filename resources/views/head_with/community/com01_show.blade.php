@extends('head_with.layouts.layout-nav')
@section('title','커뮤니티')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">커뮤니티</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 커뮤니티</span>
                <span>/ 커뮤니티</span>
            </div>
        </div>
        <div>
            @if ($type == 'add')
                <a href="#" class="btn btn-sm btn-primary shadow-sm add-btn">저장</a>
            @elseif($type == 'edit')
                <a href="#" onclick="document.detail.reset()"  class="btn btn-sm btn-outline-primary shadow-sm">입력정보 초기화</a>
                <a href="#" class="btn btn-sm btn-primary shadow-sm update-btn"><i class="bx bx-edit fs-14"></i> 수정</a>
                <a href="#" class="btn btn-sm btn-primary shadow-sm delete-btn"><i class="bx bx-trash fs-14"></i> 삭제</a>
            @endif
        </div>
    </div>
    <form name="detail">
        <input type="hidden" name="group_add" id="group_add">
        <input type="hidden" name="g_rights_value" id="g_rights_value">
        <input type="hidden" name="g_rights_view_value" id="g_rights_view_value">
        <input type="hidden" name="g_rights_write_value" id="g_rights_write_value">
        <input type="hidden" name="g_rights_comment_value" id="g_rights_comment_value">
        <input type="hidden" name="functions" id="functions">
        
        <div class="card_wrap mb-3">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">커뮤니티 관리</a>
                    </div>
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
                                        <tr>
                                            <th>게시판 아이디</th>
                                            <td>
                                                @if ($type == 'add')
                                                    <div class="flax_box inline_btn_box" style="padding-right:80px;">
                                                        <input type="text" name="board_id" id="board_id" class="form-control form-control-sm">
                                                        <a href="#" class="btn btn-sm btn-primary shadow-sm id-chk-btn" style="width:75px;">중복체크</a>
                                                    </div>
                                                    <div class="txt_box mt-1">※ 10자 이하의 영문, 숫자를 입력 (특수문자, 공백 불가)</div>
                                                @else
                                                    <div class="txt_box">{{@$board->board_id}}</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>게시판 이름</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="board_nm" id="board_nm" class="form-control form-control-sm" value="{{@$board->board_nm}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>게시판 타입</th>
                                            <td>
                                                <div class="flax_box">
                                                    <select name="board_type" id="board_type" class="form-control form-control-sm">
                                                        <option value="" @if (@$board->board_type == "") selected @endif >== 선택 ==</option>
                                                        <option value="list" @if (@$board->board_type == "list") selected @endif >리스트형</option>
                                                        <option value="gallery" @if (@$board->board_type == "gallery") selected @endif >갤러리형</option>
                                                        <option value="blog" @if (@$board->board_type == "blog") selected @endif>블로그형</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>게시판 기능</th>
                                            <td>
                                                <div class="form-inline form-check-box">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="func" id="func_reply" class="custom-control-input functions" value="2" @if (@($board->functions & 2) == "2") checked @endif />
                                                        <label class="custom-control-label" for="func_reply">답변</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="func" id="func_comment" class="custom-control-input functions" value="4" @if (@($board->functions & 4) == "4") checked @endif />
                                                        <label class="custom-control-label" for="func_comment">댓글</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="func" id="func_secret" class="custom-control-input functions" value="8" @if (@($board->functions & 8) == "8") checked @endif />
                                                        <label class="custom-control-label" for="func_secret">비밀글</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="func" id="func_upload" class="custom-control-input functions" value="16" @if (@($board->functions & 16) == "16") checked @endif />
                                                        <label class="custom-control-label" for="func_upload">이미지 업로드</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>작성자 노출</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="display_writer" id="display_writer_1" class="custom-control-input" value="1" @if (@$board->display_writer != "2") checked @endif />
                                                        <label class="custom-control-label" for="display_writer_1">이름</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="display_writer" id="display_writer_2" class="custom-control-input" value="2" @if (@$board->display_writer == "2") checked @endif />
                                                        <label class="custom-control-label" for="display_writer_2">아이디</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">댓글 작성자 노출</th>
                                            <td class="ty2">
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="display_comment_writer" id="display_comment_writer_1" class="custom-control-input" value="1" @if (@$board->display_comment_writer != "2") checked @endif />
                                                        <label class="custom-control-label" for="display_comment_writer_1">상점 정보</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="display_comment_writer" id="display_comment_writer_2" class="custom-control-input" value="2" @if (@$board->display_comment_writer == "2") checked @endif />
                                                        <label class="custom-control-label" for="display_comment_writer_2">작성자</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>예비 항목명 1</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="cfg_reserve1" id="cfg_reserve1" class="form-control form-control-sm" value="{{@$board->cfg_reserve1}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>예비 항목명 2</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="cfg_reserve2" id="cfg_reserve2" class="form-control form-control-sm" value="{{@$board->cfg_reserve2}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>예비 항목명 3</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="cfg_reserve3" id="cfg_reserve3" class="form-control form-control-sm" value="{{@$board->cfg_reserve3}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">한 페이지당 출력 갯수</th>
                                            <td class="ty2">
                                                <div class="flax_box">
                                                    <input 
                                                        type="text" 
                                                        name="row_cnt" 
                                                        id="row_cnt" 
                                                        class="form-control form-control-sm text-right" 
                                                        value="{{@$board->row_cnt}}"
                                                        onkeyup="currency(this)"
                                                    >
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">페이지 출력 갯수</th>
                                            <td class="ty2">
                                                <div class="flax_box">
                                                    <input 
                                                        type="text" 
                                                        name="page_cnt" 
                                                        id="page_cnt" 
                                                        class="form-control form-control-sm text-right" 
                                                        value="{{@$board->page_cnt}}"
                                                        onkeyup="currency(this)"
                                                    >
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>상단 디자인</th>
                                            <td>
                                                <div class="flax_box">
                                                    <textarea 
                                                        name="header_html" 
                                                        id="header_html"  
                                                        rows="10"
                                                        style="width:100%"
                                                        class="form-control form-control-sm"
                                                    >{{@$board->header_html}}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>하단 디자인</th>
                                            <td>
                                                <div class="flax_box">
                                                    <textarea 
                                                        name="footer_html" 
                                                        id="footer_html" 
                                                        rows="10"
                                                        style="width:100%"
                                                        class="form-control form-control-sm"
                                                    >{{@$board->footer_html}}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>사용여부</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_use" id="is_use1" class="custom-control-input" value="1" @if (@$board->is_use != "0") checked @endif />
                                                        <label class="custom-control-label" for="is_use1">예</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_use" id="is_use0" class="custom-control-input" value="0" @if (@$board->is_use == "0") checked @endif />
                                                        <label class="custom-control-label" for="is_use0">아니오</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card_wrap mb-3 auth-box">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">권한</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <!-- 리스트 접근 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tr>
                                            <th>리스트 접근</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="rights" id="rights_2" class="custom-control-input" value="2" @if (@$board->rights != "1") checked @endif>
                                                                <label class="custom-control-label" for="rights_2" >회원</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="rights" id="rights_1" class="custom-control-input" value="1" @if (@$board->rights == "1") checked @endif>
                                                                <label class="custom-control-label" for="rights_1">제한 없음</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_rights" id="s_rights" class="form-control form-control-sm" @if(@$board->rights == "1") disabled @endif>
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('rights', 'A')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select
                                                            name="g_rights" 
                                                            id="g_rights"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height: 100px"
                                                            @if(@$board->rights == "1") disabled @endif>
                                                            @foreach($auth['L']['A'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('rights')" class="btn btn-sm btn-secondary add-group-btn mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>리스트 제한</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md d-none d-md-block d-lg-block">
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_access" id="s_access"  class="form-control form-control-sm">
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('access', 'D')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select 
                                                            name="g_access" 
                                                            id="g_access"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height:85px;"
                                                        >
                                                            @foreach($auth['L']['D'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('access')" class="btn btn-sm btn-secondary mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>조회 접근</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="rights_view" id="rights_view2" class="custom-control-input" value="2" @if (@$board->rights_view != "1") checked @endif>
                                                                <label class="custom-control-label" for="rights_view2">회원</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="rights_view" id="rights_view1" class="custom-control-input" value="1" @if (@$board->rights_view == "1") checked @endif>
                                                                <label class="custom-control-label" for="rights_view1">제한 없음</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_rights_view" id="s_rights_view" class="form-control form-control-sm" @if(@$board->rights_view == "1") disabled @endif>
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('rights_view', 'A')" class="btn btn-sm btn-secondary add-group-btn my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select
                                                            name="g_rights_view"
                                                            id="g_rights_view"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height:85px;"
                                                            @if(@$board->rights_view == "1") disabled @endif>
                                                            @foreach($auth['R']['A'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('rights_view')" class="btn btn-sm btn-secondary add-group-btn mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>조회 제한</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md d-none d-md-block d-lg-block">
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_access_view" id="s_access_view"  class="form-control form-control-sm">
                                                        <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('access_view', 'D')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select 
                                                            name="g_access_view" 
                                                            id="g_access_view"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height:85px;"
                                                        >
                                                            @foreach($auth['R']['D'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('access_view')" class="btn btn-sm btn-secondary mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>작성 접근</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="rights_write" id="rights_write2" value="2" @if (@$board->rights_write != "1") checked @endif>
                                                                <label for="rights_write2" class="custom-control-label">회원</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="rights_write" id="rights_write1" value="1" @if (@$board->rights_write == "1") checked @endif>
                                                                <label for="rights_write1" class="custom-control-label">관리자</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_rights_write" id="s_rights_write" class="form-control form-control-sm" @if(@$board->rights_write == "1") disabled @endif>
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('rights_write', 'A')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select 
                                                            name="g_rights_write" 
                                                            id="g_rights_write"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height:85px;"
                                                            @if(@$board->rights_write == "1") disabled @endif>
                                                            @foreach($auth['W']['A'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('rights_write')" class="btn btn-sm btn-secondary mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>작성 제한</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md d-none d-md-block d-lg-block">
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_access_write" id="s_access_write"  class="form-control form-control-sm">
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('access_write', 'D')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select
                                                            name="g_access_write" 
                                                            id="g_access_write"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height:85px"
                                                        >
                                                            @foreach($auth['W']['D'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('access_write')" class="btn btn-sm btn-secondary mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>댓글작성 접근</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="rights_comment" id="rights_comment2" value="2" @if (@$board->rights_comment != "1") checked @endif>
                                                                <label for="rights_comment2" class="custom-control-label">회원</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="rights_comment" id="rights_comment1" value="1" @if (@$board->rights_comment == "1") checked @endif>
                                                                <label for="rights_comment1" class="custom-control-label">관리자</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_rights_comment" id="s_rights_comment"  class="form-control form-control-sm" @if(@$board->rights_comment == "1") disabled @endif>
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('rights_comment', 'A')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select 
                                                            name="g_rights_comment" 
                                                            id="g_rights_comment"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height: 85px;"
                                                            @if(@$board->rights_comment == "1") disabled @endif>
                                                            @foreach($auth['C']['A'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('rights_comment')" class="btn btn-sm btn-secondary mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>댓글작성 제한</th>
                                            <td>
                                                <div class="row pt-1">
                                                    <div class="col-md d-none d-md-block d-lg-block">
                                                    </div>
                                                    <div class="col-md">
                                                        <select name="s_access_comment" id="s_access_comment"  class="form-control form-control-sm">
                                                            <option value="">==그룹==</option>
                                                            @foreach($groups as $val)
                                                                <option value="{{$val->group_no}}">{{$val->group_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="addGroup('access_comment', 'D')" class="btn btn-sm btn-secondary my-2">추가</a>
                                                    </div>
                                                    <div class="col-md">
                                                        <select
                                                            name="g_access_comment" 
                                                            id="g_access_comment"  
                                                            class="form-control form-control-sm" 
                                                            multiple 
                                                            style="height:85px"
                                                        >
                                                            @foreach($auth['C']['D'] as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                        <a href="#" onclick="delGroup('access_comment')" class="btn btn-sm btn-secondary mt-2">삭제</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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
let idChk = true;
const board_id = '{{$board_id}}';

const validate = () => {
    var ff = document.detail;

    if (ff.board_id?.value == "") {
        alert("게시판 아이디를 입력하십시오.");
        ff.board_id.focus();
        return false;
    }

    if (!idChk) {
        alert("게시판 아이디를 중복체크를 해주세요.");
        return false;
    }

    if (ff.board_nm.value == "") {
        alert("게시판 이름을 입력하십시오.");
        ff.board_nm.focus();
        return false;
    }

    return true;
}

const groupAuth = (name, flag = true) => {
    $(`#s_${name}`).attr('disabled', flag);
    $(`#g_${name}`).attr('disabled', flag);
    $(`#d_${name}`).attr('disabled', flag);
}

// 그룹 권한 추가
const addGroup = (name, access) => {
    event.preventDefault();

    const sId = `#s_${name}`;
    const gId = `#g_${name}`;
    const group = $(sId);
    const selObj = $(gId)[0];

    console.log(group.val());

    if(!group.val()){
        alert("추가하실 그룹을 선택해 주십시오.");
        return false;
    }

    const val = group.val();
    const value = `${access}|${val}`;
    const text = $(`${sId} [value='${val}']`).html();
    const result = $(`${gId} [value='${value}']`).length;

    if(result > 0) {
        alert("이미 등록된 그룹입니다.");
        return;
    }

    selObj.length++;
    selObj.options[selObj.length-1].text = text;
    selObj.options[selObj.length-1].value = value;
}

// 그룹 권한 삭제
const delGroup = (name) => {
    event.preventDefault();

    const group_no = $(`#g_${name}`).val();

    console.log(name);

    if(group_no == ""){
        alert("삭제하실 그룹을 선택해 주십시오.");
        return false;
    }

    var selObj = $(`#g_${name}`)[0];

    if (selObj.length != 0){
        for(i = selObj.length-1;i >= 0; i--){
            if(selObj.options[i].value == group_no ){
                selObj.options[i] = null;
            }
        }
    } else {
        alert("삭제할 그룹이 없습니다.");
    }
}

const selectGroup = (obj,objAcess) => {
    var sel = "";

    // 허용 그룹
    for (i = 0; i < obj.length; i++) {
        obj[i].selected = true;
        sel = (i == 0) ? obj[i].value : sel + "," + obj[i].value;
    }

    // 제한 그룹
    for (i = 0; i < objAcess.length; i++) {
        objAcess[i].selected = true;
        sel = (sel == "" && i == 0) ? objAcess[i].value : sel + "," + objAcess[i].value;
    }
    return sel;
}

const valueSet = () => {
    $('#g_rights_value').val(selectGroup($('#g_rights')[0], $('#g_access')[0]));
    $('#g_rights_view_value').val(selectGroup($('#g_rights_view')[0], $('#g_access_view')[0]));
    $('#g_rights_write_value').val(selectGroup($('#g_rights_write')[0], $('#g_access_write')[0]));
    $('#g_rights_comment_value').val(selectGroup($('#g_rights_comment')[0], $('#g_access_comment')[0]));
    const functions = [];

    $('.functions:checked').each(function(data){
        functions.push(this.value);
    });

    $('#functions').val(functions.join(','));
}

$('#board_id').keyup(() => idChk = false);

$('.id-chk-btn').click(function(){
    $.ajax({    
        type: "get",
        url: `/head/community/com01/id-chk/${$('#board_id').val()}`,
        success: function(data) {
            if (data.result > 0) {
                alert("사용불가능한 아이디입니다.");
                idChk = false;
            } else {
                alert("사용가능한 아이디입니다.");
                idChk = true;
            }
        }
    });
});

$('.auth-box [type=radio]').change(function(){
    groupAuth(this.name, this.value != 2);
});

$('.update-btn').click(() => {
    if (validate() === false) return;
    if (confirm('해당 커뮤니티를 변경하시겠습니까?') === false) return;

    valueSet();

    const data = $('form[name="detail"]').serialize();

    $.ajax({
        async: true,
        type: 'put',
        url: `/head/community/com01/show/${board_id}`,
        data: data,
        success: function () {
            alert("수정되었습니다.");
            opener?.Search?.();
            location.reload();
        },
        error: function(request, status, error) {
            console.log("error")
        }
    });
});

$('.add-btn').click(() => {
    if (validate() === false) return;
    if (confirm('해당 커뮤니티를 등록하시겠습니까?') === false) return;

    valueSet();

    const data = $('form[name="detail"]').serialize();

    $.ajax({
        async: true,
        type: 'post',
        url: `/head/community/com01/show/${$('#board_id').val()}`,
        data: data,
        success: function (id) {
            alert("등록되었습니다.");
            opener?.Search?.();
            location.href = `/head/community/com01/show/edit/${id}`;
        },
        error: function(request, status, error) {
            console.log("error")
        }
    });
});

$('.delete-btn').click(() => {
    if (confirm('해당 커뮤니티를 삭제하시겠습니까?') === false) return;

    $.ajax({
        async: true,
        type: 'delete',
        url: `/head/community/com01/show/${board_id}`,
        success: function () {
            alert("삭제되었습니다.");
            opener?.Search?.();
            window.close();
        },
        error: function(request, status, error) {
            alert("게시판이 존재하지 않거나 사용여부를 '아니요'로 변경 후 삭제하여 주십시오.");
            console.log("error")
        }
    });
});
</script>
@stop
