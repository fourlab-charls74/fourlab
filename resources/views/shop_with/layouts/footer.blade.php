<footer>
    <p>2020  Handle</p>
    <a href="#" class="topbtn">맨위로 <i class="far fa-arrow-alt-circle-up ml-1"></i></a>
</footer>
<script>
    $(".topbtn").on("click", function(e){
        e.preventDefault();
        $('html, body').animate({
            scrollTop : 0
        },100);
    });
</script>