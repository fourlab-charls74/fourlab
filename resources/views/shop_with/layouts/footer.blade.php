<footer>
    <p>2020  Handle</p>
    <a href="#" class="topbtn">맨위로 <i class="far fa-arrow-alt-circle-up ml-1"></i></a>
</footer>
<script>
    let store_cd = "{{Auth('head')->user()->store_cd}}";
    let grade = "{{Auth('head')->user()->grade}}";

    $(document).ready(function(){
        openMsgPopup();
        
        setInterval(function(){
            openMsgPopup();
        }, 300000);
    });

    function openMsgPopup() {
        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/community/comm02/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code == 200) {
                        $.each(data.msgs, function(i, item){

							const popupOpened = localStorage.getItem(item.msg_cd);
							if (!popupOpened || popupOpened === 'false') {

								const url			= '/shop/community/comm02/showContent?msg_type=pop&msg_cd=' + item.msg_cd;
								const popupWindow	= window.open(url, item.msg_cd, "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");

								if (popupWindow) {
									localStorage.setItem(item.msg_cd, 'true');
								}
							}							
                            //const url = '/shop/community/comm02/showContent?msg_type=pop&msg_cd=' + item.msg_cd;
                            //const msg = window.open(url, item.msg_cd, "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
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
