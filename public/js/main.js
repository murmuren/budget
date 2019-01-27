$(document).ready(function (){
    CKEDITOR.replace('transaction_comment');
    
    $('#add_transaction').on('click', function(){
        $('#add_transaction_container').css('display','block');
    });
    
    $('.close').on({
        click: function(){
            $('.removable').css('display', 'none');
        }
    });
    
    $('#edit_transaction').on({
        click: function(){
            $(this).closest('.table_row').prev('.table_row').children().each(function(){
                var data = $(this).html();
                $(this).html('<input type="text" value="'+data+'"/>');
            });
        }
    });
    
    $('#confirm_transaction_changes').on({
        click: function(){
            $(this).closest('.table_row').prev('.table_row').children().each(function(){
                var data = $(this).find('input').val();
                console.dir(data);
                $(this).html(data);
            });
        }
    });
    
    $('#discard_transaction_changes').on({
        click: function(){
            
        }
    });
});