<footer>
    <p>2020  Handle</p>
    <a href="#" class="topbtn">맨위로 <i class="far fa-arrow-alt-circle-up ml-1"></i></a>
</footer>
<script>
    let store_cd = "{{Auth('head')->user()->store_cd}}";
    let grade = "{{Auth('head')->user()->grade}}";

    //20230317 ny - 팝업 관련 수정 중
    // setTimeout(function(){
    // }, 100);
        
    openNoticePopup();
    openMsgPopup();

    // setInterval(function(){
    //     openNoticePopup();
    //     openMsgPopup();
    // }, 300000);

        // $(document).ready(function(){
    // });

    function openNoticePopup() {
        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/stock/stk31/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code == 200) {
                        $.each(data.nos, function(i, item){
                            const url = '/shop/stock/stk31/popup_notice/' + item.ns_cd;
                            // const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=600,height=450");
                            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=600,height=500");
                        });
					} else {
						alert("공지사항 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					}
				},
				error: function(request, status, error) {
					alert("공지사항 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					console.log("error")
				}
			});
        }
    }

    function openMsgPopup() {
        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/stock/stk32/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code == 200) {
                        $.each(data.msgs, function(i, item){
                            const url = '/shop/stock/stk32/showContent?msg_type=pop&msg_cd=' + item.msg_cd;
                            // const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
                            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=580");
                        });
					} else {
						alert("메세지 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					}
				},
				error: function(request, status, error) {
					alert("메세지 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					console.log("error")
				}
			});
        }
    }

    $(".topbtn").on("click", function(e){
        e.preventDefault();
        $('html, body').animate({
            scrollTop : 0
        },100);
    });
</script>