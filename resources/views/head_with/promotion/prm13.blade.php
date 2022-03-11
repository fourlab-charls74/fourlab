@extends('head_with.layouts.layout')
@section('title','트래킹 명단 관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">트레킹 명단 관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 트레킹</span>
        <span>/ 트레킹 명단 관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>명단 추가</a>
					<a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>클래식 참여자 결제 URL 생성</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일 :</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">이벤트명/번호 :</label>

							<div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:65%;">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='s_title' value=''>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:29%;">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='s_evt_idx' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">결제번호 :</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='s_order_no' value=''>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">등록번호 :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_user_code' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">접수상태 :</label>
                            <div class="flax_box">
                                <select name="s_evt_state" class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value="1">입금예정</option>
                                    <option value="5">접수후보</option>
                                    <option value="9">후보결제대기</option>
                                    <option value="10">접수완료</option>
                                    <option value="20">확정대기</option>
                                    <option value="30">확정완료</option>
                                    <option value="-10">결제오류</option>
                                    <option value="-20">신청취소</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">접수자명(대표) :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_user_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">휴대폰(대표) :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_mobile' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">성별 :</label>
                            <div class="flax_box">
                                <select name="s_sex" class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value="M">남성</option>
                                    <option value="F">여성</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">국가 :</label>
                            <div class="flax_box">
                                <select name="s_country" class="form-control form-control-sm">
                                    <option value=''>--</option>
									<option value="39178">South Korea</option>
									<option value="39004">Sweden</option>
									<option value="39078">HongKong</option>
									<option value="39005">Denmark</option>
									<option value="39006">Norway</option>
									<option value="39007">Andorra</option>
									<option value="39008">Angola</option>
									<option value="39009">Anguilla</option>
									<option value="39010">Antigua &amp; Barbuda</option>
									<option value="39011">Argentina</option>
									<option value="39012">Armenia</option>
									<option value="39013">Aruba</option>
									<option value="39014">Australia</option>
									<option value="39015">Azerbaijan</option>
									<option value="39016">Bahamas</option>
									<option value="39017">Bahrain</option>
									<option value="39018">Bangladesh</option>
									<option value="39019">Barbados</option>
									<option value="39020">Belgium</option>
									<option value="39021">Belize</option>
									<option value="39022">Benin</option>
									<option value="39023">Bermuda</option>
									<option value="39024">Bhutan</option>
									<option value="39025">Bolivia</option>
									<option value="39026">Bosnia and Herzegovina</option>
									<option value="39027">Botswana</option>
									<option value="39028">Brazil</option>
									<option value="39029">British Virgin Islands</option>
									<option value="39030">Brunei</option>
									<option value="39031">Bulgaria</option>
									<option value="39032">Burkina Faso</option>
									<option value="39033">Burma</option>
									<option value="39034">Burundi</option>
									<option value="39035">Cayman Islands</option>
									<option value="39036">Central African Republic</option>
									<option value="39037">Chile</option>
									<option value="39038">Colombia</option>
									<option value="39039">Comorerna</option>
									<option value="39040">Cook Islands</option>
									<option value="39041">Costa Rica</option>
									<option value="39042">Cypros</option>
									<option value="39043">Dominica</option>
									<option value="39044">Dominican Republic</option>
									<option value="39045">Ecuador</option>
									<option value="39046">Egypt</option>
									<option value="39047">Equatorial Guinea</option>
									<option value="39048">El Salvador</option>
									<option value="39049">Ivory Coast (Cote d'Ivoire)</option>
									<option value="39050">England</option>
									<option value="39051">Eritrea</option>
									<option value="39052">Estonia</option>
									<option value="39053">Ethiopia</option>
									<option value="39054">Falkland Is. (Malvinas)</option>
									<option value="39055">Fiji</option>
									<option value="39056">Philippines</option>
									<option value="39057">Finland</option>
									<option value="39058">France</option>
									<option value="39059">French Guiana</option>
									<option value="39060">French Polynesia</option>
									<option value="39061">Faroe Islands</option>
									<option value="39062">Gabon</option>
									<option value="39063">Gambia</option>
									<option value="39064">Georgia</option>
									<option value="39065">Ghana</option>
									<option value="39066">Gibraltar</option>
									<option value="39067">Greece</option>
									<option value="39068">Grenada</option>
									<option value="39069">Greenland</option>
									<option value="39070">Guadeloupe</option>
									<option value="39071">Guam</option>
									<option value="39072">Guatemala</option>
									<option value="39073">Guinea</option>
									<option value="39074">Guinea-Bissau</option>
									<option value="39075">Guyana</option>
									<option value="39076">Haiti</option>
									<option value="39077">Honduras</option>
									<option value="39078">Hongkong</option>
									<option value="39079">India</option>
									<option value="39080">Indonesia</option>
									<option value="39081">Irac</option>
									<option value="39082">Iran</option>
									<option value="39083">Ireland</option>
									<option value="39084">Island</option>
									<option value="39085">Isle of Man</option>
									<option value="39086">Israel</option>
									<option value="39087">Italy</option>
									<option value="39088">Jamaica</option>
									<option value="39089">Japan</option>
									<option value="39090">Jemen</option>
									<option value="39091">Jordania</option>
									<option value="39092">Cambodia</option>
									<option value="39093">Cameroon</option>
									<option value="39094">Canada</option>
									<option value="39095">Kenya</option>
									<option value="39096">China</option>
									<option value="39097">Kiribati</option>
									<option value="39098">Congo</option>
									<option value="39099">Croatia</option>
									<option value="39100">Cuba</option>
									<option value="39101">Kuwait</option>
									<option value="39102">Laos</option>
									<option value="39103">Lesotho</option>
									<option value="39104">Latvia</option>
									<option value="39105">Lebanon</option>
									<option value="39106">Liberia</option>
									<option value="39107">Libya</option>
									<option value="39108">Liechtenstein</option>
									<option value="39109">Lithuania</option>
									<option value="39110">Luxemburg</option>
									<option value="39111">Madagascar</option>
									<option value="39112">Maced</option>
									<option value="39113">Malawi</option>
									<option value="39114">Malaysia</option>
									<option value="39115">Maldives</option>
									<option value="39116">Mali</option>
									<option value="39117">Malta</option>
									<option value="39118">Marocko</option>
									<option value="39119">Marshall Islands</option>
									<option value="39120">Martinique</option>
									<option value="39121">Mauritius</option>
									<option value="39122">Mayotte</option>
									<option value="39123">Mexico</option>
									<option value="39124">Micronesia </option>
									<option value="39125">Mozambique</option>
									<option value="39126">Moldova</option>
									<option value="39127">Monaco</option>
									<option value="39128">Mongolia</option>
									<option value="39129">Namibia</option>
									<option value="39130">Nauru</option>
									<option value="39131">Netherlands</option>
									<option value="39132">Netherlands Antilles</option>
									<option value="39133">Nepal</option>
									<option value="39134">Nicaragua</option>
									<option value="39135">Niger</option>
									<option value="39136">Nigeria</option>
									<option value="39137">North Korea</option>
									<option value="39138">Norway</option>
									<option value="39139">New Zealand</option>
									<option value="39140">Oman</option>
									<option value="39141">Pakistan</option>
									<option value="39142">Panama</option>
									<option value="39143">Papua New Guinea</option>
									<option value="39144">Paraguay</option>
									<option value="39145">Peru</option>
									<option value="39146">Pitcairn Island</option>
									<option value="39147">Poland</option>
									<option value="39148">Portugal</option>
									<option value="39149">Puerto Rico</option>
									<option value="39150">Reunion</option>
									<option value="39151">Romania</option>
									<option value="39152">Rwanda</option>
									<option value="39153">Russia</option>
									<option value="39154">Saint Christopher och Nevis</option>
									<option value="39155">Saint Helena</option>
									<option value="39156">Saint Lucia</option>
									<option value="39157">Saint Vincent och Grenadinerna</option>
									<option value="39158">Saint-Pierre-et-Miquelon</option>
									<option value="39159">Salomonoarna</option>
									<option value="39160">Samoa</option>
									<option value="39161">Soo Tomo och Principe</option>
									<option value="39162">Saudi Arabia</option>
									<option value="39163">Schweiz</option>
									<option value="39164">Senegal</option>
									<option value="39165">Serbia</option>
									<option value="39166">Sierra Leone</option>
									<option value="39167">Singapore</option>
									<option value="39168">Scottland</option>
									<option value="39169">Slovakia</option>
									<option value="39170">Slovenia</option>
									<option value="39171">Spain</option>
									<option value="39172">Sri Lanka</option>
									<option value="39173">Great Britain</option>
									<option value="39174">Sudan</option>
									<option value="39175">Surinam</option>
									<option value="39176">Swaziland</option>
									<option value="39177">South Africa</option>
									<option value="39178">South Korea</option>
									<option value="39179">Syria</option>
									<option value="39180">Taiwan</option>
									<option value="39181">Tanzania</option>
									<option value="39182">Tchad</option>
									<option value="39183">Thailand</option>
									<option value="39184">Czech Republic</option>
									<option value="39185">Togo</option>
									<option value="39186">Tonga</option>
									<option value="39187">Trinidad &amp; Tobago</option>
									<option value="39188">Tunisia</option>
									<option value="39189">Turkey</option>
									<option value="39190">Turkmenistan</option>
									<option value="39191">Turks and Caicos Is</option>
									<option value="39192">Tuvalu</option>
									<option value="39193">Germany</option>
									<option value="39194">Uganda</option>
									<option value="39195">Ukraine</option>
									<option value="39196">Hungaria</option>
									<option value="39197">Uruguay</option>
									<option value="39198">USA</option>
									<option value="39199">Uzbekistan</option>
									<option value="39200">Wales</option>
									<option value="39201">Wallis and Futuna</option>
									<option value="39202">Vanuatu</option>
									<option value="39203">Venezuela</option>
									<option value="39204">Vietnam</option>
									<option value="39205">Belarus</option>
									<option value="39206">Zambia</option>
									<option value="39207">Zimbabwe</option>
									<option value="39208">Austria</option>
									<option value="39209">East Timor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="resul_btn_wrap d-sm-none">
                    <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                </div>
            </div>

        </div>

        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>명단 추가</a>
			<a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>클래식 참여자 결제 URL 생성</a><br><br>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

    </div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
				<div class="fr_box flax_box">
					<div class="mr-1">
						<select id="s1_evt_state" class="form-control form-control-sm">
							<option value="">전체</option>
							<option value="1">입금예정</option>
							<option value="5">접수후보</option>
							<option value="9">후보결제대기</option>
							<option value="10">접수완료</option>
							<option value="20">확정대기</option>
							<option value="30">확정완료</option>
							<option value="-10">결제오류</option>
							<option value="-20">신청취소</option>
						</select>
					</div>
					<a href="#" onclick="ChangeState()" class="btn-sm btn btn-primary">상태 변경</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
    var columns = [
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width:28,
			pinned:'left'
		},
		{
			headerName: '#',
			width:50,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			pinned:'left'
		},
        {headerName: "번호", field: "evt_idx",width:50},
        {headerName: "이벤트명", field: "title",width:200},
        {headerName: "결제번호", field: "order_no", cellStyle:StyleOrderNo, width:180,
            cellRenderer: function(params) {
				return '<a href="#" onClick="Popprm13(\''+ params.value +'\')">'+ params.value+'</a>'
            }
        },
        {headerName: "접수상태", field: "evt_state_nm", cellStyle:chgStateStyle, width:90},
        {headerName: "접수방법", field: "kind", width:80},
        {headerName: "아이디", field: "user_id", cellStyle:chgUseridStyle, width:120},
        {headerName: "등록번호", field: "user_code", width:80, cellStyle:{"text-align":"center"},
            cellRenderer: function(params) {
				return '<a href="#" onClick="PopUpdprm13(\''+ params.data.order_no +'\',\''+ params.value +'\')">'+ params.value+'</a>'
            }
        },
        {headerName: "접수자명", field: "user_nm", width:100},
        {headerName: "접수자명(영문)", field: "en_nm", width:120},
        {headerName: "연령", field: "ckind", width:50},
        {headerName: "휴대폰번호", field: "mobile", width:120},
        {headerName: "이메일", field: "email", width:150},
        {headerName: "성별", field: "sex", width:50},
        {headerName: "국가", field: "country", width:100},
        {headerName: "생년월일", field: "birthdate", width:80},
        {headerName: "긴급연락처", field: "em_phone", width:120},
        {headerName: "출발그룹", field: "group_nm", width:60},
        {headerName: "주소", field: "addr", width:180},
        {headerName: "채식여부", field: "dietary_yn", width:60},
        {headerName: "등록일", field: "regdate", width:150},
        {headerName: "evt_mem_idx", field: "evt_mem_idx", hidden:true},
    ];

    function Popprm13(item)
    {
        const url='/head/promotion/prm13/show/' + item;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

	function PopUpdprm13(item1, item2)
	{
        const url='/head/promotion/prm13/show/' + item1 + '/' + item2;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=730");
	}

	function chgStateStyle(params)
    {
		var font_color = "";

        if(params.value !== undefined){
			var font_color = "#0000";
			switch(params.data.evt_state_nm){
				case "접수후보":
					font_color = "#3E9900"; break;
                case "접수완료":
					font_color = "#1F4C00"; break;
                case "확정대기":
					font_color = "#4C4CFF"; break;
                case "확정완료":
					font_color = "#0000FF"; break;
                case "결제오류":
					font_color = "#FF0000"; break;
                case "신청취소":
					font_color = "#FF0000"; break;
			}

			return {
				'color': font_color,
				'font-weight' : '400'
			}
			
		}
    }

	function chgUseridStyle(params)
    {
		return {
			'background-color': '#FDE9E8',
		}
    }

	// 그룹 셀 컬러 지정 시작
	var _styleOrdNoCnt		= 0;
	var _styleColorIndex	= -1;
	function StyleOrderNo(params)
	{
		if( params.value !== undefined )
		{
			var colors	= {
				0:"#ffff00",
				1:"#C5FF9D",
			}
			var rowIndex	= params.node.rowIndex;
			if( rowIndex > 0 && params.data.ord_no_bg_color === undefined )
			{
				var rowNode	= params.api.getDisplayedRowAtIndex(rowIndex-1);
				if( params.value == rowNode.data.order_no )
				{
					_styleColorIndex	= _styleOrdNoCnt % 2;
					params.data['ord_no_bg_color']	= colors[_styleColorIndex];
					rowNode.data['ord_no_bg_color']	= colors[_styleColorIndex];
				} 
				else 
				{
					if( _styleColorIndex >= 0 )
					{
						_styleOrdNoCnt++;
						_styleColorIndex	= -1;
					}
				}
			}
			if( params.data.ord_no_bg_color !== undefined || params.data.ord_no_bg_color != '' )
			{
				return {
					'background-color': params.data.ord_no_bg_color
				}
			}
		}
	}
	// 그룹 쉘 컬러 지정 끝

	function ChangeState()
	{
		var checkRows = gx.gridOptions.api.getSelectedRows();

		if( checkRows.length === 0 )
		{
            alert("접수상태를 변경할 명단을 선택해주세요.");
            return;
		}

		var s1_evt_state	= $("#s1_evt_state").val();

		if( s1_evt_state == "" )
		{
            alert("접수상태를 선택해주세요.");
            return;
		}

		if(confirm("선택하신 명단의 접수상태를 변경하시겠습니까?")) 
		{
			var evt_mem_idxs	= checkRows.map(function(row) 
			{
				return row.evt_mem_idx;
			});

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/promotion/prm13',
				data: {
					"evt_mem_idxs[]" : evt_mem_idxs,
					s1_evt_state : s1_evt_state
				},
				success: function (data) {
					if( data.code == "200" )
					{
						alert("접수상태를 변경하였습니다.");
					} 
					else 
					{
						alert("접수상태 변경을 실패하였습니다.");
					}

					Search();
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
		}

	}

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm13/search', data);
    }

</script>
@stop
