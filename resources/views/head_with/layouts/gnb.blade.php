<div id="gnb" class="d-none">
	<div class="search-tab flex-center py-4">
		<div class="search-box py-1 px-3">
			<input type="search" name="gnb_search_keyword" id="gnb_search_keyword" class="form-control border-0 fs-14" placeholder="메뉴 검색">
			<button type="button" id="gnb_search_btn" class="text-primary fs-16 border-0 bg-transparent px-2"><i class="fas fa-search fa-sm"></i></button>
		</div>
	</div>
	<div class="list-tab px-5 pt-3">
		<ul class="list-1"></ul>
	</div>
</div>
<script>
	let gnb_menu_obj = {};
@if(Cache::has('head_gnb'))
	gnb_menu_obj = <?= json_encode(Cache::get('head_gnb')) ?>;
@endif
	const gnb_list = Object.keys(gnb_menu_obj || {}).map(a => ({
		title: gnb_menu_obj[a].kor_nm,
		icon: gnb_menu_obj[a].icon,
		sub: gnb_menu_obj[a].kind === 'P' 
			? [{
				title: gnb_menu_obj[a].kor_nm,
				url: gnb_menu_obj[a].action,
				target: gnb_menu_obj[a].target,
			}]
			: Object.keys(gnb_menu_obj[a].sub || {}).length > 0 
				? Object.keys(gnb_menu_obj[a].sub).map(aa => ({
						title: gnb_menu_obj[a].sub[aa].kor_nm,
						url: gnb_menu_obj[a].sub[aa].action,
						target: gnb_menu_obj[a].sub[aa].target,
						sub: Object.keys(gnb_menu_obj[a].sub[aa].sub || {}).length > 0 
							? Object.keys(gnb_menu_obj[a].sub[aa].sub).map(aaa => ({
									title: gnb_menu_obj[a].sub[aa].sub[aaa].kor_nm,
									url: gnb_menu_obj[a].sub[aa].sub[aaa].action,
									target: gnb_menu_obj[a].sub[aa].sub[aaa].target,
								}))
							: undefined,
					}))
				: undefined,
	}));
	
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
				title: aa.title?.includes(kw) || (aa.sub ? aa.sub.filter(aaa => aaa.title.includes(kw)).length > 0 : 0) ? aa.title : '',
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
													return aaa + (ccc.title ? `<li><a href="${ccc.url || ''}"${ccc.target ? ' target="' + ccc.target + '"' : ''}>${ccc.title.split('').map(tt => `<span class="${kw.includes(tt) ? 'text-danger' : ''}">` + tt + '</span>').join('')}</a></li>` : '');
												}, '')}
											</ul>
										</div>
									</li>
								` : `
									<li><a href="${cc.url || ''}"${cc.target ? ' target="' + cc.target + '"' : ''}>${cc.title.split('').map(tt => `<span class="${kw.includes(tt) ? 'text-danger' : ''}">` + tt + '</span>').join('')}</a></li>
								`) : '');
							}, '')}
						</ul>
					</div>
				</li>
			`;
		}, ''));
	}
</script>
