<div id="gnb" class="d-none">
{{--<div id="gnb">--}}
	<div class="search-tab flex-center py-4">
		<div class="search-box py-1 px-3">
			<input type="search" name="gnb_search_keyword" id="gnb_search_keyword" class="form-control border-0 fs-14" placeholder="메뉴 검색">
			<button type="button" id="gnb_search_btn" class="text-primary fs-16 border-0 bg-transparent px-2"><i class="fas fa-search fa-sm"></i></button>
		</div>
	</div>
	<div class="list-tab px-5 pt-3">
		<ul class="list-1">
{{--			<li>--}}
{{--				<div class="list-box">--}}
{{--					<p class="list-title">상품/전시</p>--}}
{{--					<ul class="list-2">--}}
{{--						<li><a href="#">상품정보관리</a></li>--}}
{{--						<li><a href="#">세일관리</a></li>--}}
{{--						<li><a href="#">원사이즈 및 클리어런스</a></li>--}}
{{--						<li><a href="#">상품정보고시</a></li>--}}
{{--						<li><a href="#">상품도매</a></li>--}}
{{--						<li><a href="#">상품 전시</a></li>--}}
{{--						<li><a href="#">섹션 전시</a></li>--}}
{{--						<li><a href="#">기획전 전시</a></li>--}}
{{--						<li>--}}
{{--							<div class="list-box">--}}
{{--								<p class="list-title">제휴</p>--}}
{{--								<ul class="list-3">--}}
{{--									<li><a href="#">네이버 지식쇼핑</a></li>--}}
{{--									<li><a href="#">크리테오</a></li>--}}
{{--								</ul>--}}
{{--							</div>--}}
{{--						</li>--}}
{{--						<li>--}}
{{--							<div class="list-box">--}}
{{--								<p class="list-title">사방넷</p>--}}
{{--								<ul class="list-3">--}}
{{--									<li><a href="#">상품</a></li>--}}
{{--									<li><a href="#">주문</a></li>--}}
{{--								</ul>--}}
{{--							</div>--}}
{{--						</li>--}}
{{--					</ul>--}}
{{--				</div>--}}
{{--			</li>--}}
		</ul>
	</div>
</div>
<script>
	const gnb_list = [
		{title: "홈", icon: "bx-home", sub: [
			{title: "대시보드", url: "#"},
			{title: "Profile", url: "#"},
			{title: "My Log", url: "#"},
		]},		
		{title: "기준정보", icon: "bx-file", sub: [
			{title: "품목", url: "#"},
			{title: "업체", url: "#"},
			{title: "브랜드", url: "#"},
			{title: "카테고리", url: "#"},
			{title: "FAQ", url: "#"},
			{title: "광고", sub: [
				{title: "광고", url: "#"},
				{title: "광고할인", url: "#"},
			]},
			{title: "템플릿", url: "#"},
		]},
		{title: "재고", icon: "bx-archive", sub: [
			{title: "발주", url: "#"},
			{title: "입고", url: "#"},
			{title: "재고관리", url: "#"},
			{title: "입출고내역", url: "#"},
			{title: "재고입고알림", url: "#"},
			{title: "XMD", sub: [
				{title: "상품매칭", url: "#"},
				{title: "재고파일 관리", url: "#"},
				{title: "재고예외 관리", url: "#"},
				{title: "재고 등록", url: "#"},
				{title: "재고 등록 오류 관리", url: "#"},
				{title: "재고 비교 모니터링", url: "#"},
			]},
			{title: "상품별 판매율", url: "#"},
		]},
		{title: "상품/전시", icon: "bx-task", sub: [
			{title: "상품정보관리", url: "#"},
			{title: "세일관리", url: "#"},
			{title: "원사이즈 및 클리어런스", url: "#"},
			{title: "상품정보고시", url: "#"},
			{title: "상품도매", url: "#"},
			{title: "상품 전시", url: "#"},
			{title: "섹션 전시", url: "#"},
			{title: "기획전 전시", url: "#"},
			{title: "제휴", sub: [
				{title: "네이버 지식쇼핑", url: "#"},
				{title: "크리테오", url: "#"},
			]},
			{title: "사방넷", sub: [
				{title: "상품", url: "#"},
				{title: "주문", url: "#"},
			]},
		]},
	];
	
	$(document).ready(function () {
		setGnbList('', gnb_list);

		$("#gnb_search_keyword").on("keyup", function (e) {
			if (e.keyCode === 13) {
				const kw = e.target.value;
				searchForKeyword(kw, gnb_list);
			}
		});
		$("#gnb_search_btn").on("click", function (e) {
			const kw = $("#gnb_search_keyword").val();
			searchForKeyword(kw, gnb_list);
		});
	});
	
	const searchForKeyword = (kw, list) => {
		const searched_list = list.map(a => ({
			...a, sub: a.sub ? a.sub.map(aa => ({
				...aa,
				title: aa.title.includes(kw) || (aa.sub ? aa.sub.filter(aaa => aaa.title.includes(kw)).length > 0 : 0) ? aa.title : '',
				sub: aa.sub ? aa.sub.map(aaa => ({
					...aaa, title: aaa.title.includes(kw) ? aaa.title : ''
				})) : undefined
			})) : undefined
		}));
		setGnbList(kw, searched_list);
	}
	
	const setGnbList = (kw, list) => {
		$("#gnb .list-tab .list-1").html(list.reduce((a, c) => {
			return a + `
				<li>
					<div class="list-box">
						<p class="list-title"><i class="bx ${c.icon || ''} fs-18 mr-2"></i> ${c.title || ''}</p>
						<ul class="list-2">
							${(c.sub || []).reduce((aa, cc) => {
								return aa + (cc.title ? (cc.sub ? `
										<li>
											<div class="list-box">
												<p class="list-title">${cc.title.split('').map(tt => `<span class="${kw.includes(tt) ? 'text-danger' : ''}">` + tt + '</span>').join('')}</p>
												<ul class="list-3">
													${cc.sub.reduce((aaa, ccc) => {
														return aaa + (ccc.title ? `<li><a href="${ccc.url || ''}">${ccc.title.split('').map(tt => `<span class="${kw.includes(tt) ? 'text-danger' : ''}">` + tt + '</span>').join('')}</a></li>` : '');
													}, '')}
												</ul>
											</div>
										</li>
									` : `
										<li><a href="${cc.url || ''}">${cc.title.split('').map(tt => `<span class="${kw.includes(tt) ? 'text-danger' : ''}">` + tt + '</span>').join('')}</a></li>
									`) : '');
							}, '')}
						</ul>
					</div>
				</li>
			`;
		}, ''));
	}
</script>
