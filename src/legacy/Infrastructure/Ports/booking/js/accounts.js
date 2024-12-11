

$(function(){
    $("form").on( "submit", function(){
        if(CryptoJS !== undefined){
            $("input[name='Pass']").val(CryptoJS.SHA512($( "#Pass" ).val()));
            $("input[name='NewPass']").val(CryptoJS.SHA512($( "#NewPass" ).val()));
            $("input[name='ReNewPass']").val(CryptoJS.SHA512($( "#ReNewPass" ).val()));
        }
    });
});
