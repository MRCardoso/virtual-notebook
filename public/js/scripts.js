$(document).ready(function()
{
    $("#display-error").on('click', function()
    {
        var $this = $(this).find('i');
        if( $this.hasClass('fa-angle-right') ){
            $this.removeClass('fa-angle-right').addClass('fa-angle-down');
            $('#debug-error').show();
        } else{
            $this.removeClass('fa-angle-down').addClass('fa-angle-right');
            $('#debug-error').hide();
        }
    });
    
    $(".remove-link").on("click", function (e)
    {
        e.preventDefault();
        console.log($(this).data("url"));
        $("#form-delete").attr('action',$(this).data("url"));
    });

    $(".ico-password").on('click', function(e){
        var $el = $(this).parent().find("input");
        if( $el.attr('type') == 'password' )
        {
            $el.attr('type', 'text');
            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        }
        else
        {
            $el.attr('type', 'password');
            $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    $("#btn-box-password").on('click', function()
    {
        var $this = $(this).find('i');
        if( $this.hasClass('fa-angle-right') ){
            $this.removeClass('fa-angle-right').addClass('fa-angle-down');
            $('#box-password').slideDown(300).find('input[type=password]').attr("disabled", false);
        } else{
            $this.removeClass('fa-angle-down').addClass('fa-angle-right');
            $('#box-password').slideUp(300).find('input[type=password]').attr("disabled", true);
        }
    });
})