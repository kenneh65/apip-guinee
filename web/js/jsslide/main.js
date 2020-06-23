$("document").ready(function() {
    /*
    Première version
    
    $("#slideshow ul#slide li").eq(0).show();
    $("#slideshow ul#item li a").eq(0).addClass('select');
    
    $("#slideshow ul#item li a").click(function() {
        var slideClique = $(this).parent().index();
        
        $("#slideshow ul#slide li").hide();
        $("#slideshow ul#item li a").removeClass('select');
        
        $("#slideshow ul#slide li").eq(slideClique).fadeIn(200);
        $("#slideshow ul#item li a").eq(slideClique).addClass('select');
    });
    */
    
    
    /* Deuxième version */
    $("#slideshow ul#slide li").eq(0).show();
    $("#slideshow ul#item li a").eq(0).addClass('select');
    var slideSelect = $("#slideshow ul#item li a.select").index();
    animate();
    
    $("#slideshow ul#item li a").click(function() {
        if (slideSelect != $(this).parent().index()) {
            reset();
            change($(this).parent().index());
        }
    });
                                       
    function reset() {
        $("#slideshow ul#slide li").hide();
        $("#slideshow ul#item li a").removeClass('select');
    }
    
    function change(slide) {
        $("#slideshow ul#slide li").eq(slide).fadeIn(200);
        $("#slideshow ul#item li a").eq(slide).addClass('select');
        slideSelect = slide;
    }
    
    function animate() {
        setTimeout(function() {
            slideSelect = slideSelect == ($("#slideshow ul#slide li").length -1) ? 0 : slideSelect +1; 
            
            reset();
            change(slideSelect);
            animate();
        }, 4000);
    }
    
    
});